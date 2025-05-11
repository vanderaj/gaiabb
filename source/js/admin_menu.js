/**
 * GaiaBB
 * Copyright (c) 2009-2021 The GaiaBB Group
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

var strIDs = getCookie('UltimaBBAdminMenu');
if (strIDs && (strIDs != '')) {
    arrIds = strIDs.split(',');
    for (var i = 0; i <= arrIds.length; i++) {
        id = arrIds[i];
        el = document.getElementById(id);
        if (el) {
            el.className = el.className.replace('shown', '');
            el.className = el.className + (('' + el.className).length) > 0 ? ' shown' : 'shown';
        }
    }
}

function setCookie(name, value, expires, path, domain, secure)
{
    var curCookie = name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires.toGMTString() : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
    document.cookie = curCookie;
}

function getCookie(name)
{
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0) {
            return null;
        }
    } else {
        begin += 2;
    }
    var end = document.cookie.indexOf(";", begin);
    if (end == -1) {
        end = dc.length;
    }
    return unescape(dc.substring(begin + prefix.length, end));
}

function getIdsOfElements(els)
{
    ids = [];
    for (var i = 0; i < els.length; i++) {
        ids[i] = els[i].id;
    }
    return ids;
}

function getElementsByClassName(classname)
{
    var a = [];
    var re = new RegExp('\\b' + classname + '\\b');
    var els;
    if (typeof (document.all) != 'undefined') {
        els = document.all;
    } else {
        els = document.getElementsByTagName("*");
    }

    for (var n = 0; n < els.length; n++) {
        if (re.test(els[n].className)) {
            a.push(els[n]);
        }
    }

    return a;
}

var strIDs = getCookie('UltimaBBAdminMenu');

window.onunload = function () {
    var els = getElementsByClassName('shown');
    var ids = getIdsOfElements(els);
    var strIDs = ids.join(',');
    setCookie('UltimaBBAdminMenu', strIDs)
}
