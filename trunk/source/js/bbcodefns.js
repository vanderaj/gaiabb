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

defmode = "normalmode";

if (defmode == "advmode")
{
    helpmode = false;
    normalmode = false;
    advmode = true;
}
else if (defmode == "helpmode")
{
    helpmode = true;
    normalmode = false;
    advmode = false;
}
else
{
    helpmode = false;
    normalmode = true;
    advmode = false;
}

function chmode(swtch)
{
    if (swtch == 1)
    {
        advmode = false;
        normalmode = false;
        helpmode = true;
        alert(bbcode_helpmode);
    }
    else if (swtch == 0)
    {
        helpmode = false;
        normalmode = false;
        advmode = true;
        alert(bbcode_advmode);
    }
    else if (swtch == 2)
    {
        helpmode = false;
        advmode = false;
        normalmode = true;
        alert(bbcode_normode);
    }
}

function AddText(text, el)
{
    if (el.createTextRange && el.caretPos)
    {
        var caretPos = el.caretPos
        el.caretPos.text = el.caretPos.text.charAt(el.caretPos.text.length - 1) == ' ' ? text + ' ' : text;
    }
    else
    {
        el.value  += text;
    }
    el.focus();
}

function email()
{
    if (helpmode)
    {
        alert(bbcode_help_email);
    }
    else if (advmode)
    {
        AddText("[email] [/email]", messageElement);
    }
    else
    {
        txt2=prompt(bbcode_prompt_email,"");
        if (txt2!=null)
        {
            txt=prompt(bbcode_prompt_email_email,"user@example.com");
            if (txt2=="")
            {
                AddText("[email]"+txt+"[/email]", messageElement);
            }
            else
            {
                AddText("[email="+txt+"]"+txt2+"[/email]", messageElement);
            }
        }
    }
}

function chsize(size)
{
    if (helpmode)
    {
        alert(bbcode_help_size);
    }
    else if (advmode)
    {
        AddText("[size="+size+"] [/size]", messageElement);
    }
    else
    {
        txt=prompt(bbcode_prompt_size+size,"Text");
        if (txt!=null)
        {
            AddText("[size="+size+"]"+txt+"[/size]", messageElement);
        }
    }
}

function chfont(font)
{
    if (helpmode)
    {
        alert(bbcode_help_font);
    }
    else if (advmode)
    {
        AddText("[font="+font+"] [/font]", messageElement);
    }
    else
    {
        txt=prompt(bbcode_prompt_font,"Text");
        if (txt!=null)
        {
            AddText("[font="+font+"]"+txt+"[/font]", messageElement);
        }
    }
}

function bold()
{
    if (helpmode)
    {
        alert(bbcode_help_bold);
    }
    else if (advmode)
    {
        AddText("[b] [/b]", messageElement);
    }
    else
    {
        txt=prompt(bbcode_prompt_bold,"Text");
        if (txt!=null)
        {
            AddText("[b]"+txt+"[/b]", messageElement);
        }
    }
}

function italicize()
{
    if (helpmode)
    {
        alert(bbcode_help_italic);
    }
    else if (advmode)
    {
        AddText("[i] [/i]", messageElement);
    }
    else
    {
        txt=prompt(bbcode_prompt_italic,"Text");
        if (txt!=null) {
            AddText("[i]"+txt+"[/i]", messageElement);
        }
    }
}

function quote()
{
    if (helpmode)
    {
        alert(bbcode_help_quote);
    }
    else if (advmode)
    {
        AddText("\r[quote]\r[/quote]", messageElement);
    }
    else
    {
        txt=prompt(bbcode_prompt_quote,"Text");
        if (txt!=null)
        {
            AddText("\r[quote]\r"+txt+"\r[/quote]", messageElement);
        }
    }
}

function chcolor(color)
{
    if (helpmode)
    {
        alert(bbcode_help_color);
    }
    else if (advmode)
    {
        AddText("[color="+color+"] [/color]", messageElement);
    }
    else
    {
        txt=prompt(bbcode_prompt_color+color,"Text");
        if (txt!=null)
        {
            AddText("[color="+color+"]"+txt+"[/color]", messageElement);
        }
    }
}

function center()
{
    if (helpmode)
    {
        alert(bbcode_help_center);
    }
    else if (advmode)
    {
        AddText("[align=center] [/align]", messageElement);
    }
    else
    {
        txt=prompt(bbcode_prompt_center,"Text");
        if (txt!=null)
        {
            AddText("\r[align=center]"+txt+"[/align]", messageElement);
        }
    }
}

function hyperlink()
{
    if (helpmode)
    {
        alert(bbcode_help_link);
    }
    else if (advmode)
    {
        AddText("[url] [/url]", messageElement);
    }
    else
    {
        txt2=prompt(bbcode_prompt_link1,"");
        if (txt2!=null)
        {
            txt=prompt(bbcode_prompt_link2,"http://");
            if (txt!=null)
            {
                if (txt2=="")
                {
                    AddText("[url]"+txt+"[/url]", messageElement);
                }
                else
                {
                    AddText("[url="+txt+"]"+txt2+"[/url]", messageElement);
                }
            }
        }
    }
}

function image()
{
    if (helpmode)
    {
        alert(bbcode_help_image);
    }
    else if (advmode)
    {
        AddText("[img] [/img]", messageElement);
    }
    else
    {
        txt=prompt(bbcode_prompt_image,"http://");
        if (txt!=null) {
            AddText("\r[img]"+txt+"[/img]", messageElement);
        }
    }
}

function code()
{
    if (helpmode)
    {
        alert(bbcode_help_code);
    }
    else if (advmode)
    {
        AddText("\r[code]\r[/code]", messageElement);
    }
    else
    {
        txt=prompt(bbcode_prompt_code,"");
        if (txt!=null)
        {
            AddText("\r[code]"+txt+"[/code]", messageElement);
        }
    }
}

function list()
{
    if (helpmode)
    {
        alert(bbcode_help_list);
    }
    else if (advmode)
    {
        AddText("\r[list]\r[*]\r[*]\r[*]\r[/list]", messageElement);
    }
    else
    {
        st=prompt(bbcode_prompt_list_start,"");
        if ((st!="") && (st!="A") && (st!="a") && (st!="1") && (st!=null))
        {
            st = prompt(bbcode_prompt_list_error,"");
        }

        if (st != null)
        {
            if (st == "")
            {
                AddText("\r[list]\r\n", messageElement);
            }
            else
            {
                AddText("\r[list="+st+"]\r", messageElement);
            }

            txt = "1";
            while((txt!="") && (txt!=null))
            {
                txt = prompt(bbcode_prompt_list_end, "");
                if ((txt!="") && (txt!=null))
                {
                    AddText("[*]"+txt+"\r", messageElement);
                }
            }

            if ((st!="") && (st!=null))
            {
                AddText("[/list="+st+"]\r\n", messageElement);
            }
            else
            {
                AddText("[/list]\r\n", messageElement);
            }
        }
    }
}

function underline()
{
      if (helpmode)
      {
        alert(bbcode_help_underline);
    }
    else if (advmode)
    {
        AddText("[u] [/u]", messageElement);
    }
    else
    {
        txt=prompt(bbcode_prompt_underline,"Text");
        if (txt!=null)
        {
            AddText("[u]"+txt+"[/u]", messageElement);
        }
    }
}

function storeCaret(textEl)
{
    if (textEl.createTextRange) textEl.caretPos = document.selection.createRange().duplicate();
}

function setfocus()
{
    document.input.message.focus();
}
