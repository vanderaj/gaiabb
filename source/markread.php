<?php

/**
 * GaiaBB
 * Copyright (c) 2011-2022 The GaiaBB Group
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

loadtpl('');

nav();
btitle();

if (X_GUEST) {
    error($lang['textnoaction']);
}

$ubblvb = $onlinetime;
put_cookie('ubblvb', $ubblvb, $ubblvb + 600, $cookiepath, $cookiedomain, false, X_SET_HEADER);
$lastvisit = $onlinetime;
$lastvisit2 = $onlinetime;
redirect('index.php', 0);
