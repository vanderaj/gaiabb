/**
 * GaiaBB
 * Copyright (c) 2009 The GaiaBB Group
 * http://www.GaiaBB.com
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

function add()
{
    var to = opener.document.getElementById('msgto');
    var from = document.getElementsByName('users');
    var add = new Array();
    var j = 0;

    if (from.length > 0)
    {
        for (var i = 0; i < from.length; i++)
        {
            if (from[i].checked == 1) {
                add[j++] = from[i].value;
            }
        }
    }

    if (to.value != '')
    {
        old = to.value.split(', ');
        for (i=0;i<old.length;i++)
        {
            for (j=0;j<add.length;j++)
            {
                if (add[j] == old[i])
                {
                    add.splice(j,1);
                    break;
                }
            }
        }

        if (add.length > 0)
        {
            to.value += ', '+add.join(', ');
        }
    }
    else
    {
        to.value = add.join(', ');
    }

    opener.aBookOpen = false;
    self.close();
}
