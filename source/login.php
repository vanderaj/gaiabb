<?php
/**
 * GaiaBB
 * Copyright (c) 2009-2021 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Forked from UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * Forked from XMB
 * Copyright (c) 2001 - 2021 The XMB Development Team
 * https://forums.xmbforum2.com/
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
 *
 **/
// phpcs:disable PSR1.Files.SideEffects
define('CACHECONTROL', 'nocache');

require_once 'header.php';

loadtpl('login', 'login_incorrectdetails');

$shadow = shadowfx();
$shadow2 = shadowfx2();
$meta = metaTags();

smcwcache();

eval('$css = "' . template('css') . '";');

nav($lang['textlogin']);
btitle($lang['textlogin']);

eval('echo "' . template('header') . '";');

if (X_MEMBER) {
    error($lang['plogtuf'], false);
}

$errMessage = '';

if (onSubmit('loginsubmit')) {
    $oToken->assertToken();

    $errMessage = $authC->checkExcessiveLogins();

    if (empty($errMessage)) {
        $errMessage = $authC->login();
    }
}

if (!empty($errMessage)) {
    echo $errMessage;
}

eval('echo stripslashes("' . template('login') . '");');

loadtime();
eval('echo "' . template('footer') . '";');
