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

?>
<h1><?php echo __('Documentation'); ?></h1>
<h3>ABOUT IT</h3>
<p>Djg_core plugin contains a set of functions used in my other plugins.<br />It's necessary to run many of them.</p>
<h3>VERSIONS</h3>
<ul>
<li>0.0.1 - added executeSql()
<li>0.0.2 - added translation methods
<li>0.0.3 - changed uniqeId() for PHP less 5.2
<li>0.0.4 - added mimeByExt()
<li>0.0.5 - added copyDirectory()
<li>0.0.6 - added jquery-ui-1.8.18.custom.min.js()
<li>0.0.7 - email protector
<li>0.0.7a - v.0.7.7 compatible
<li>0.0.8 - email protector how to, remove selfTest
</ul>
<h3>AUTHOR</h3>
<p>kreacjawww.pl - Michał Uchnast - djgprv@gmail.com</p>
<h3>LICENSE</h3>
<p>This plugin is licensed under the GPLv3 License.<br /><http://www.gnu.org/licenses/gpl.html></p>