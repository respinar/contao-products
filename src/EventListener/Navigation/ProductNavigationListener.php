<?php

/**
 * changelanguage Extension for Contao Open Source CMS
 *
 * @copyright  Copyright (c) 2008-2016, terminal42 gmbh
 * @author     terminal42 gmbh <info@terminal42.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link       http://github.com/terminal42/contao-changelanguage
 */

namespace Respinar\ProductsBundle\EventListener\Navigation;

use Respinar\ProductsBundle\Model\ProductCatalogModel;
use Respinar\ProductsBundle\Model\ProductModel;
use Contao\PageModel;
use Haste\Input\Input;
use Terminal42\ChangeLanguage\EventListener\Navigation\AbstractNavigationListener;

/**
 * Translate URL parameters for product items
 */
class ProductNavigationListener extends AbstractNavigationListener
{
    /**
     * @inheritdoc
     */
    protected function getUrlKey()
    {
        return 'items';
    }

    /**
     * @inheritdoc
     */
    protected function findCurrent()
    {
        $alias = (string) Input::getAutoItem($this->getUrlKey(), false, true);

        if ('' === $alias) {
            return null;
        }

        /** @var PageModel $objPage */
        global $objPage;

        if (($archives = ProductCatalogModel::findBy('jumpTo', $objPage->id)) === null) {
            return null;
        }

        // Fix Contao bug that returns a collection (see contao-changelanguage#71)
        $options = ['limit' => 1, 'return' => 'Model'];

        return ProductModel::findPublishedByParentAndIdOrAlias($alias, $archives->fetchEach('id'), $options);
    }

    /**
     * @inheritdoc
     */
    protected function findPublishedBy(array $columns, array $values = array(), array $options = array())
    {
        return ProductModel::findOneBy(
            $this->addPublishedConditions($columns, ProductModel::getTable()),
            $values,
            $options
        );
    }
}