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

function setCheckboxes(the_form, the_elements, do_check)
{
    var elts = document.forms[the_form].elements[the_elements]
    var elts_cnt = elts.length;
    if (elts_cnt) {
        for (var i = 0; i < elts_cnt; i++) {
            elts[i].checked = do_check;
        }
    }
    return true;
}

function invertSelection(the_form, element_name)
{
    var elements = document.forms[the_form].elements[element_name];
    var count = elements.length;
    if (count) {
        for (var i = 0; i < count; i++) {
            if (elements[i].checked == true) {
                elements[i].checked = false;
            } else {
                elements[i].checked = true;
            }
        }
    } else {
        if (elements.checked == true) {
            elements.checked = false;
        } else {
            elements.checked = true;
        }
    }
    return true;
}

function setCheckboxes(the_form, do_check)
{
    var elts = document.forms[the_form].elements['pm_select[]']
    var elts_cnt = elts.length;
    if (elts_cnt) {
        for (var i = 0; i < elts_cnt; i++) {
            elts[i].checked = do_check;
        }
    }
    return true;
}

function Popup(url, window_name, window_width, window_height)
{
    settings =
        "toolbar=no,location=yes,directories=no," +
        "status=yes,menubar=yes,scrollbars=yes," +
        "resizable=yes,width=" + window_width + ",height=" + window_height;
    NewWindow = window.open(url, window_name, settings);
}

function icon(theicon)
{
    AddText('', '', theicon, messageElement);
}

var aBookOpen = false;
var aBookLink = '';
function aBook()
{
    if (aBookOpen == true) {
        aBookLink.close();
        aBookOpen = false;
    } else {
        if (typeof sendMode === "undefined" || sendMode != true) {
            aBookLink = window.open('address.php', 'aBook', "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=450,height=400");
        } else {
            aBookLink = window.open('address.php?action=add2pm', 'aBook', "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=450,height=400");
        }
        aBookOpen = true;
    }

    return false;
}

function textCounter(field, cntfield, maxlimit)
{
    count = document.getElementById(cntfield);
    if (field.value.length > maxlimit) {
        field.value = field.value.substring(0, maxlimit);
    } else {
        count.innerHTML = field.value.length;
    }
}

var sendMode = true;
self.name = 'mainwindow';
