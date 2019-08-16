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

function Popup(url, window_name, window_width, window_height) {
    settings =
        "toolbar=no,location=yes,directories=no," +
        "status=yes,menubar=yes,scrollbars=yes," +
        "resizable=yes,width=" + window_width + ",height=" + window_height;
    NewWindow = window.open(url, window_name, settings);
}

function icon(theicon) {
    AddText(theicon, messageElement);
}

function AddText(text, el) {
    if (el.createTextRange && el.caretPos) {
        el.caretPos.text = (el.caretPos.text.charAt(el.caretPos.text.length - 1) == ' ' ? text + ' ' : text);
    }
    else {
        el.value += text;
    }
    el.focus();
}
