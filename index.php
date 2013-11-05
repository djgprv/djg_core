<?php
/*
 * Wolf CMS - Content Management Simplified. <http://www.wolfcms.org>
 * Copyright (C) 2008-2010 Martijn van der Kleijn <martijn.niji@gmail.com>
 *
 * This file is part of Wolf CMS. Wolf CMS is licensed under the GNU GPLv3 license.
 * Please see license.txt for the full license text.
 */

/* Security measure */
if (!defined('IN_CMS')) { exit(); }

/**
 * @package Plugins
 * @subpackage djg_core
 *
 * @author Michał Uchnast <djgprv@gmail.com>
 * @copyright kreacjawww.pl, 2012
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 license
 */

Plugin::setInfos(array(
    'id'          => 'djg_core',
    'title'       => __('[djg] Core'),
    'description' => __('Core'),
    'version'     => '0.0.8',
   	'license'     => 'GPL',
	'author'      => 'Michał Uchanst',
    'website'     => 'http://www.kreacjawww.pl/',
    'update_url'  => 'http://kreacjawww.pl/public/wolf_plugins/plugin-versions.xml'
));
Plugin::addController('djg_core', __('[djg] Core'), 'administrator', false);
AutoLoader::addFolder(PLUGINS_ROOT.'/djg_core/models/');
?>