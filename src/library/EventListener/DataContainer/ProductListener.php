<?php
/**
 * changelanguage Extension for Contao Open Source CMS
 *
 * @copyright  Copyright (c) 2008-2016, terminal42 gmbh
 * @author     terminal42 gmbh <info@terminal42.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link       http://github.com/terminal42/contao-changelanguage
 */

namespace Respinar\Products\EventListener\DataContainer;

use Contao\Date;
use Contao\Model;
use Respinar\Products\Model\ProductModel;
use Terminal42\ChangeLanguage\EventListener\DataContainer\AbstractChildTableListener; 

class ProductListener extends AbstractChildTableListener
{
    /**
     * @inheritdoc
     */
    protected function getTitleField()
    {
        return 'title';
    }

    /**
     * @inheritdoc
     */
    protected function getSorting()
    {
        return 'date DESC';
    }

    /**
     * @inheritdoc
     *
     * @param NewsModel   $current
     * @param NewsModel[] $models
     */
    protected function formatOptions(Model $current, Model\Collection $models)
    {
        $sameDay  = $GLOBALS['TL_LANG']['tl_product']['sameDay'];
        $otherDay = $GLOBALS['TL_LANG']['tl_product']['otherDay'];
        $dayBegin = strtotime('0:00', $current->date);
        $options  = [$sameDay => [], $otherDay => []];

        foreach ($models as $model) {
            $group = strtotime('0:00', $model->date) === $dayBegin ? $sameDay : $otherDay;

            $options[$group][$model->id] = sprintf(
                '%s (%s) [%s]',
                $model->title,
                $model->code,
                Date::parse($GLOBALS['TL_CONFIG']['dateFormat'], $model->date)
            );
        }

        return $options;
    }
}
