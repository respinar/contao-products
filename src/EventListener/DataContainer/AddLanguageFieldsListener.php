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

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsHook('loadDataContainer')]
class AddLanguageFieldsListener
{
    private const TABLE = 'tl_product_catalog';

    public function __construct(private readonly ParameterBagInterface $parameterBag)
    {
    }

    public function __invoke(string $table): void
    {
        if (self::TABLE !== $table) {
            return;
        }

        $bundles = $this->parameterBag->get('kernel.bundles');

        if (!isset($bundles['Terminal42ChangeLanguageBundle'])) {
            return;
        }

        $GLOBALS['TL_DCA'][$table]['fields']['master'] = [
            'exclude' => true,
            'inputType' => 'select',
            'eval' => [
                'includeBlankOption' => true,
                'blankOptionLabel' => &$GLOBALS['TL_LANG']['tl_product_catalog']['isMaster'],
                'tl_class' => 'w50',
            ],
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
            'relation' => ['type' => 'hasOne', 'table' => 'tl_product_catalog', 'field' => 'id', 'load' => 'lazy'],
        ];

        $GLOBALS['TL_DCA'][$table]['fields']['language'] = [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 32, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 32, 'default' => ''],
        ];

        PaletteManipulator::create()
            ->addLegend('language_legend', 'redirect_legend', PaletteManipulator::POSITION_AFTER)
            ->addField('master', 'language_legend', PaletteManipulator::POSITION_APPEND)
            ->addField('language', 'language_legend', PaletteManipulator::POSITION_APPEND)
            ->applyToPalette('default', $table)
        ;
    }
}
