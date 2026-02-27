<?php

declare(strict_types=1);

/*
 * This file is part of Contao Products Bundle.
 *
 * (c) Hamid Peywasti 2024 <hamid@respinar.com>
 *
 * @license MIT
 */

namespace Respinar\ProductsBundle\Dca;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\System;

final class CommentFields
{
    public static function addTo(string $table): void
    {
        // Do nothing if the Comments Bundle is not installed.
        if (!self::isCommentsBundleInstalled()) {
            return;
        }

        $GLOBALS['TL_DCA'][$table]['palettes']['__selector__'][] = 'allowComments';
        $GLOBALS['TL_DCA'][$table]['subpalettes']['allowComments'] = 'notify,sortOrder,perPage,moderate,bbcode,requireLogin,disableCaptcha';

        $GLOBALS['TL_DCA'][$table]['fields']['allowComments'] = [
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true],
            'sql' => ['type' => 'boolean', 'default' => false],
        ];

        $GLOBALS['TL_DCA'][$table]['fields']['notify'] = [
            'inputType' => 'select',
            'options' => ['notify_admin', 'notify_author', 'notify_both'],
            'reference' => &$GLOBALS['TL_LANG'][$table],
            'eval' => ['tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 32, 'default' => 'notify_admin'],
        ];

        $GLOBALS['TL_DCA'][$table]['fields']['sortOrder'] = [
            'inputType' => 'select',
            'options' => ['ascending', 'descending'],
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval' => ['tl_class' => 'w50 clr'],
            'sql' => ['type' => 'string', 'length' => 32, 'default' => 'ascending'],
        ];

        $GLOBALS['TL_DCA'][$table]['fields']['perPage'] = [
            'inputType' => 'text',
            'eval' => ['rgxp' => 'natural', 'tl_class' => 'w50'],
            'sql' => ['type' => 'smallint', 'unsigned' => true, 'default' => 0],
        ];

        $GLOBALS['TL_DCA'][$table]['fields']['moderate'] = [
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ];

        $GLOBALS['TL_DCA'][$table]['fields']['bbcode'] = [
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ];

        $GLOBALS['TL_DCA'][$table]['fields']['requireLogin'] = [
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ];

        $GLOBALS['TL_DCA'][$table]['fields']['disableCaptcha'] = [
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
            'sql' => ['type' => 'boolean', 'default' => false],
        ];

        PaletteManipulator::create()
            ->addLegend('comments_legend', null, PaletteManipulator::POSITION_APPEND, true)
            ->addField('allowComments', 'comments_legend', PaletteManipulator::POSITION_APPEND)
            ->applyToPalette('default', $table)
        ;
    }

    private static function isCommentsBundleInstalled(): bool
    {
        $bundles = System::getContainer()->getParameter('kernel.bundles');

        return isset($bundles['ContaoCommentsBundle']);
    }
}
