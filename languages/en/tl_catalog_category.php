<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package   catalog
 * @author    Hamid Abbaszadeh
 * @license   GNU/LGPL
 * @copyright 2014-2015
 */


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_catalog_category']['title']     = array('Catalog Title', 'Please enter the catalog title.');
$GLOBALS['TL_LANG']['tl_catalog_category']['jumpTo']    = array('Redirect page','Please choose the list page to which visitors will be redirected when clicking a menu.');
$GLOBALS['TL_LANG']['tl_catalog_category']['protected'] = array('Protect catalog','Show catalog items to certain member groups only.');
$GLOBALS['TL_LANG']['tl_catalog_category']['groups']    = array('Allowed member groups','These groups will be able to see the menu items in this catalog.');
$GLOBALS['TL_LANG']['tl_catalog_category']['master']    = array('Master catalog','Please define the master catalog to allow language switching.');
$GLOBALS['TL_LANG']['tl_catalog_category']['language']  = array('Language','Please enter the language according to the RFC3066 format (e.g. en, en-us or en-cockney).');


/**
 * References
 */
$GLOBALS['TL_LANG']['tl_catalog_category']['isMaster']	= 'This is a master catalog';
$GLOBALS['TL_LANG']['tl_catalog_category']['isSlave']	= 'Master catalog is "%s"';



/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_catalog_category']['title_legend']     = 'Catalog Title';
$GLOBALS['TL_LANG']['tl_catalog_category']['redirect_legend']  = 'Redirect';
$GLOBALS['TL_LANG']['tl_catalog_category']['protected_legend'] = 'Access protection';


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['tl_catalog_category']['new']    = array('New catalog','Create a new catalog');
$GLOBALS['TL_LANG']['tl_catalog_category']['show']   = array('Catalog details','Show the details of catalog ID %s');
$GLOBALS['TL_LANG']['tl_catalog_category']['edit']   = array('Edit catalog','Edit catalog ID %s');
$GLOBALS['TL_LANG']['tl_catalog_category']['copy']   = array('Duplicate catalog','Duplicate catalog ID %s');
$GLOBALS['TL_LANG']['tl_catalog_category']['delete'] = array('Delete catalog', 'Delete catalog ID %s');
