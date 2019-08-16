/**
 * GaiaBB
 * Copyright (c) 2009 The GaiaBB Group
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
 *
 **/

var isInit = false;
var attachNode = '';
var layer = '';
var clicked = 0;

function init() {
    attachNode = document.getElementById('address_add');
    layer = document.getElementById('addresses');
    isInit = true;
}

function add() {
    if (!isInit) {
        init();
    }

    if (++clicked >= 10) {
        window.alert("Max 10 addresses");
        return false;
    }
    else {
        var newChild = layer.appendChild(attachNode.cloneNode(true));
        newChild.childNodes[1].value = '';
    }
}
