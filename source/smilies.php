<?php

/**
 * GaiaBB
 * Copyright (c) 2011-2025 The GaiaBB Group
 * https://github.com/vanderaj/gaiabb
 *
 * Based off UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * Based off XMB
 * Copyright (c) 2001 - 2004 The XMB Development Team
 * http://www.xmbforum.com
 *
 * This file is part of GaiaBB
 *
 *    GaiaBB is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    GaiaBB is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with GaiaBB.  If not, see <http://www.gnu.org/licenses/>.
 **/

define('ROOT', './');

require_once ROOT . 'header.php';

loadtpl(
    'popup_footer',
    'popup_header',
    'smilies',
    'functions_smilieinsert',
    'functions_smilieinsert_smilie'
);

$shadow = shadowfx();
$meta = metaTags();

smcwcache();

nav($lang['smiliesnav']);
btitle($lang['smiliesnav']);

if (X_GUEST) {
    error($lang['textnoaction']);
}

$header = '';
eval('$css = "' . template('css') . '";');
eval('$header = "' . template('popup_header') . '";');
eval('$footer = "' . template('popup_footer') . '";');
$CONFIG['smtotal'] = 0;
$smilies = smilieinsert();
eval('$misc = "' . template('smilies') . '";');
echo $header;
echo $misc;
echo $footer;
exit;
