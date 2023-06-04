<?php

/**
 * product_categories extension for Contao Open Source CMS
 *
 * Copyright (C) 2011-2014 Codefog
 *
 * @package product_categories
 * @author  Webcontext <http://webcontext.com>
 * @author  Codefog <info@codefog.pl>
 * @author  Kamil Kuzminski <kamil.kuzminski@codefog.pl>
 * @license LGPL
 */

/**
 * Table tl_product_categories
 */
$GLOBALS['TL_DCA']['tl_product_categories'] = array
(

    // Config
    'config' => array
    (
        'dataContainer'               => 'Table',
        'sql' => array
        (
            'keys' => array
            (
                'category_id' => 'index',
                'product_id' => 'index'
            )
        )
    ),

    // Fields
    'fields' => array
    (
        'category_id' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ),
        'product_id' => array
        (
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        )
    )
);
