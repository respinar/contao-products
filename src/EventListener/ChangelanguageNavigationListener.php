<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\Date;
use Contao\Input;
use Contao\PageModel;
use Respinar\ProductsBundle\Model\CatalogModel;
use Respinar\ProductsBundle\Model\ProductModel;
use Terminal42\ChangeLanguage\Event\ChangelanguageNavigationEvent;

/**
 * Translates the product URL parameters for the ChangeLanguage navigation.
 *
 * @see https://extensions.terminal42.ch/docs/changelanguage/en/developers/
 */
#[AsHook('changelanguageNavigation')]
class ChangelanguageNavigationListener
{
    public function __construct(private readonly TokenChecker $tokenChecker)
    {
    }

    public function onChangelanguageNavigation(ChangelanguageNavigationEvent $event): void
    {
        $current = $this->findCurrent();

        if (!$current) {
            return;
        }

        $navigationItem = $event->getNavigationItem();

        if ($navigationItem->isCurrentPage()) {
            $event->getUrlParameterBag()->setUrlAttribute($this->getUrlKey(), $current->alias ?: $current->id);

            return;
        }

        // Remove the product alias from the URL if there is no actual reader page
        if (!$navigationItem->isDirectFallback()) {
            $event->getUrlParameterBag()->removeUrlAttribute($this->getUrlKey());
        }

        /** @var CatalogModel $catalog */
        $catalog = $current->getRelated('pid');

        if (0 === (int) $catalog->master) {
            $mainId = (int) $current->id;
            $masterId = (int) $current->pid;
        } else {
            $mainId = (int) $current->languageMain;
            $masterId = (int) $catalog->master;
        }

        // Abort if the current record has no translated version
        if (0 === $mainId || 0 === $masterId) {
            $navigationItem->setIsDirectFallback(false);

            return;
        }

        $targetPage = $navigationItem->getTargetPage();

        if (null === $targetPage) {
            return;
        }

        $t = ProductModel::getTable();

        $translated = $this->findPublishedBy(
            [
                "($t.id=? OR $t.languageMain=?)",
                \sprintf(
                    '%s.pid=(SELECT id FROM %s WHERE (id=? OR master=?) AND jumpTo=?)',
                    $t,
                    CatalogModel::getTable(),
                ),
            ],
            [$mainId, $mainId, $masterId, $masterId, $targetPage->id],
        );

        if (!$translated) {
            $navigationItem->setIsDirectFallback(false);

            return;
        }

        $event->getUrlParameterBag()->setUrlAttribute($this->getUrlKey(), $translated->alias ?: $translated->id);
    }

    /**
     * Find the product that is currently being viewed.
     */
    private function findCurrent(): ProductModel|null
    {
        $alias = $this->getAutoItem();

        if ('' === $alias) {
            return null;
        }

        /** @var PageModel $objPage */
        global $objPage;

        if (null === ($catalogs = CatalogModel::findBy('jumpTo', $objPage->id))) {
            return null;
        }

        return ProductModel::findPublishedByParentAndIdOrAlias(
            $alias,
            $catalogs->fetchEach('id'),
            ['limit' => 1, 'return' => 'Model'],
        );
    }

    /**
     * @param array<string>         $columns
     * @param array<string|int>     $values
     * @param array<string, string> $options
     */
    private function findPublishedBy(array $columns, array $values = [], array $options = []): ProductModel|null
    {
        return ProductModel::findOneBy(
            $this->addPublishedConditions($columns, ProductModel::getTable()),
            $values,
            $options,
        );
    }

    /**
     * @param array<string> $columns
     *
     * @return array<string>
     */
    private function addPublishedConditions(array $columns, string $table): array
    {
        if (!$this->tokenChecker->isPreviewMode()) {
            $time = Date::floorToMinute();
            $columns[] = "$table.published='1'";
            $columns[] = "($table.start='' OR $table.start<='$time')";
            $columns[] = "($table.stop='' OR $table.stop>'".($time + 60)."')";
        }

        return $columns;
    }

    private function getAutoItem(): string
    {
        $strKey = $this->getUrlKey();

        if (
            !isset($GLOBALS['TL_CONFIG']['useAutoItem'])
            || (
                $GLOBALS['TL_CONFIG']['useAutoItem']
                && isset($GLOBALS['TL_AUTO_ITEM'])
                && \in_array($strKey, $GLOBALS['TL_AUTO_ITEM'], true)
            )
        ) {
            $strKey = 'auto_item';
        }

        return (string) Input::get($strKey, false, true);
    }

    private function getUrlKey(): string
    {
        return isset($GLOBALS['TL_CONFIG']['useAutoItem']) ? 'items' : 'auto_item';
    }
}
