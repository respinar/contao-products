<?php

/**
 * changelanguage Extension for Contao Open Source CMS
 *
 * @copyright  Copyright (c) 2008-2016, terminal42 gmbh
 * @author     terminal42 gmbh <info@terminal42.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 * @link       http://github.com/terminal42/contao-changelanguage
 */

namespace Respinar\Products\EventListener;

use Terminal42\ChangeLanguage\EventListener\DataContainer\MissingLanguageIconListener;
use Terminal42\ChangeLanguage\EventListener\DataContainer\ParentTableListener;

class CallbackSetupListener
{
    private static $listeners = [
        'tl_product_catalog'    => ['Terminal42\ChangeLanguage\EventListener\DataContainer\ParentTableListener'],
        'tl_product'            => ['Respinar\Products\EventListener\DataContainer\ProductListener'],
    ];

    /**
     * @var MissingLanguageIconListener
     */
    private $labelListener;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->labelListener = new MissingLanguageIconListener();
    }

    /**
     * Callback for loadDataContainer hook.
     *
     * @param string $table
     */
    public function onLoadDataContainer($table)
    {
        $this->labelListener->register($table);

        if (array_key_exists($table, self::$listeners)) {
            foreach (self::$listeners[$table] as $class) {

                /** @var AbstractTableListener $listener */
                $listener = new $class($table);
                $listener->register();
            }
        }
    }
}