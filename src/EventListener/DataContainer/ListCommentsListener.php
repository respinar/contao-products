<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Symfony\Component\Routing\RouterInterface;

/**
 * Shows a link to the parent product in the "tl_comments" moderation list.
 *
 * @see \Contao\tl_comments::listComments()
 */
#[AsHook('listComments')]
class ListCommentsListener
{
    public function __construct(
        private readonly Connection $connection,
        private readonly RouterInterface $router,
    ) {
    }

    public function __invoke(array $arrRow): string
    {
        if ('tl_product' !== ($arrRow['source'] ?? null)) {
            return '';
        }

        $parent = (int) ($arrRow['parent'] ?? 0);

        if (0 === $parent) {
            return '';
        }

        $title = $this->connection->fetchOne('SELECT title FROM tl_product WHERE id = ?', [$parent]);

        if (false === $title) {
            return '';
        }

        $url = $this->router->generate('contao_backend', [
            'do' => 'products',
            'table' => 'tl_product',
            'act' => 'edit',
            'id' => $parent,
        ]);

        $onClick = ' onclick="Backend.openModalIframe({ title: \'&nbsp;\', url: this.href + \'&amp;popup=1&amp;nb=1\' }); return false;"';

        return ' – <a href="'.StringUtil::specialcharsUrl($url).'"'.$onClick.'>'.StringUtil::specialchars((string) $title).'</a>';
    }
}
