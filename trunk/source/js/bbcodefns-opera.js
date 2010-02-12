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

var URL_REGEXP_GOES_HERE = new RegExp('(.*)://(.*)');
var defmode = 'normal';

if (defmode == 'advanced') {
    helpmode = false;
    normalmode = false;
    advmode = true;
} else if (defmode == 'help') {
    helpmode = true;
    normalmode = false;
    advmode = false;
} else {
    helpmode = false;
    normalmode = true;
    advmode = false;
}

function chmode(switchMode) {
    if (switchMode == 1) {
        advmode = false;
        normalmode = false;
        helpmode = true;
        alert(bbcode_helpmode);
    } else if (switchMode == 0) {
        helpmode = false;
        normalmode = false;
        advmode = true;
        alert(bbcode_advmode);
    } else if (switchMode == 2) {
        helpmode = false;
        advmode = false;
        normalmode = true;
        alert(bbcode_normode);
    }
}

function AddText(bbFirst, bbLast, text, el) {
    var len = el.textLength;
    var start = el.selectionStart;
    var end = el.selectionEnd;
    var pre = el.value.substring(0, start);
    var post = el.value.substring(end, len);
    el.value = pre + bbFirst + text + bbLast + post;
    el.focus();
}

function wrapText(prePend, apPend, el) {
    var len = el.textLength;
    var start = el.selectionStart;
    var end = el.selectionEnd;
    var pre = el.value.substring(0, start);
    var mid = el.value.substring(start, end);
    var post = el.value.substring(end, len);
    el.value = pre + prePend + mid + apPend + post;
    el.focus();
}

function hasSelection(el) {
    if (el.selectionEnd-el.selectionStart > 0) {
        return true;
    } else {
        return false;
    }
}

function fetchSelection(el) {
    return el.value.substring(el.selectionStart, el.selectionEnd);
}

function email() {
    if (helpmode) {
        alert(bbcode_help_email);
    } else if (advmode) {
        if (hasSelection(messageElement)) {
            if (fetchSelection(messageElement).match(/(.+)@(.+)/) != undefined) {
                wrapText('[email]', '[/email]', messageElement);
            } else {
                wrapText('[email=user@example.com]', '[/email]', messageElement);
            }
        } else {
            AddText('[email]', '[/email]', ' ', messageElement);
        }
    } else {
        if (hasSelection(messageElement)) {
            if (fetchSelection(messageElement).match(/(.+)@(.+)/) != undefined) {
                address = prompt(bbcode_prompt_email_email, fetchSelection(messageElement));
                if (address != undefined) {
                    while (address.length == 0 || address.match(/(.+)@(.+)/) == undefined) {
                        address = prompt(bbcode_prompt_email_error, fetchSelection(messageElement));
                    }
                }
                if (address != undefined) {
                    desc = prompt(bbcode_prompt_email_desc, '');
                    if (desc != undefined) {
                        if (desc.length == 0) {
                            if (address == fetchSelection(messageElement)) {
                                wrapText('[email]', '[/email]', messageElement);
                            } else {
                                AddText('[email]', '[/email]', address, messageElement);
                            }
                        } else {
                            if (address == fetchSelection(messageElement)) {
                                wrapText('[email=', ']'+desc+'[/email]', messageElement);
                            } else {
                                AddText('[email='+address+']', '[/email]', desc, messageElement);
                            }
                        }
                    }
                }
            } else {
                address = prompt(bbcode_prompt_email_email, 'user@example.com');
                if (address != undefined) {
                    while (address.length == 0 || address.match(/(.+)@(.+)/) == undefined) {
                        address = prompt(bbcode_prompt_email_error, address);
                    }
                }
                if (address != undefined) {
                    desc = prompt(bbcode_prompt_email_desc, fetchSelection(messageElement));
                    if (desc != undefined) {
                        if (desc == fetchSelection(messageElement)) {
                            wrapText('[email='+address+']', '[/email]', messageElement);
                        } else {
                            AddText('[email='+address+']', '[/email]', desc, messageElement);
                        }
                    }
                }
            }
        } else {
            address = prompt(bbcode_prompt_email_email, 'user@example.com');
            if (address != undefined) {
                while (address.length == 0 || address.match(/(.+)@(.+)/) == undefined) {
                    address = prompt(bbcode_prompt_email_error, address);
                }
            }
            if (address != undefined) {
                desc = prompt(bbcode_prompt_email_desc, '');
                if (desc != undefined) {
                    if (desc.length == 0) {
                        AddText('[email]', '[/email]', address, messageElement);
                    } else {
                        AddText('[email='+address+']', '[/email]', desc, messageElement);
                    }
                }
            }
        }
    }
}

function chsize(size) {
    if (helpmode) {
        alert(bbcode_help_size);
    } else if (advmode) {
        if (hasSelection(messageElement)) {
            wrapText('[size='+size+']', '[/size]', messageElement);
        } else {
            AddText('[size='+size+']', '[/size]', ' ', messageElement);
        }
    } else {
        if (hasSelection(messageElement)) {
            text = prompt(bbcode_prompt_size+size, fetchSelection(messageElement));
            if (text != '' && text != undefined) {
                if (text == fetchSelection(messageElement)) {
                    wrapText('[size='+size+']', '[/size]', messageElement);
                } else {
                    AddText('[size='+size+']', '[/size]', text, messageElement);
                }
            }
        } else {
            text = prompt(bbcode_prompt_size+size, "Text");
            if (text != '' && text != undefined) {
                AddText('[size='+size+']', '[/size]', text, messageElement);
            }
        }
    }
}

function chfont(font) {
    if (helpmode) {
        alert(bbcode_help_font);
    } else if (advmode) {
        if (hasSelection(messageElement)) {
            wrapText('[font='+font+']', '[/font]', messageElement);
        } else {
            AddText('[font='+font+']', '[/font]', ' ', messageElement);
        }
    } else {
        if (hasSelection(messageElement)) {
            text = prompt(bbcode_prompt_font+font, fetchSelection(messageElement));
            if (text != '' && text != undefined) {
                if (text == fetchSelection(messageElement)) {
                    wrapText('[font='+font+']', '[/font]', messageElement);
                } else {
                    AddText('[font='+font+']', '[/font]', text, messageElement);
                }
            }
        } else {
            text = prompt(bbcode_prompt_font+font, "Text");
            if (text != '' && text != undefined) {
                AddText('[font='+font+']', '[/font]', text, messageElement);
            }
        }
    }
}

function chcolor(color) {
    if (helpmode) {
        alert(bbcode_help_color);
    } else if (advmode) {
        if (hasSelection(messageElement)) {
            wrapText('[color='+color+']', '[/color]', messageElement);
        } else {
            AddText('[color='+color+']', '[/color]', ' ', messageElement);
        }
    } else {
        if (hasSelection(messageElement)) {
            text = prompt(bbcode_prompt_color+color, fetchSelection(messageElement));
            if (text != '' && text != undefined) {
                if (text == fetchSelection(messageElement)) {
                    wrapText('[color='+color+']', '[/color]', messageElement);
                } else {
                    AddText('[color='+color+']', '[/color]', text, messageElement);
                }
            }
        } else {
            text = prompt(bbcode_prompt_color+color, "Text");
            if (text != '' && text != undefined) {
                AddText('[color='+color+']', '[/color]', text, messageElement);
            }
        }
    }
}

function bold() {
    if (helpmode) {
        alert(bbcode_help_bold);
    } else if (advmode) {
        if (hasSelection(messageElement)) {
            wrapText('[b]', '[/b]', messageElement);
        } else {
            AddText('[b]', '[/b]', ' ', messageElement);
        }
    } else {
        if (hasSelection(messageElement)) {
            text = prompt(bbcode_prompt_bold, fetchSelection(messageElement));
            if (text != '' && text != undefined) {
                if (text == fetchSelection(messageElement)) {
                    wrapText('[b]', '[/b]', messageElement);
                } else {
                    AddText('[b]', '[/b]', text, messageElement);
                }
            }
        } else {
            text = prompt(bbcode_prompt_bold, 'Text');
            if (text != '' && text != undefined) {
                AddText('[b]', '[/b]', text, messageElement);
            }
        }
    }
}

function italicize() {
    if (helpmode) {
        alert(bbcode_help_italic);
    } else if (advmode) {
        if (hasSelection(messageElement)) {
            wrapText('[i]', '[/i]', messageElement);
        } else {
            AddText('[i]', '[/i]', ' ', messageElement);
        }
    } else {
        if (hasSelection(messageElement)) {
            text = prompt(bbcode_prompt_italic, fetchSelection(messageElement));
            if (text != '' && text != undefined) {
                if (text == fetchSelection(messageElement)) {
                    wrapText('[i]', '[/i]', messageElement);
                } else {
                    AddText('[i]', '[/i]', text, messageElement);
                }
            }
        } else {
            text = prompt(bbcode_prompt_italic, 'Text');
            if (text != '' && text != undefined) {
                AddText('[i]', '[/i]', text, messageElement);
            }
        }
    }
}

function underline() {
    if (helpmode) {
        alert(bbcode_help_underline);
    } else if (advmode) {
        if (hasSelection(messageElement)) {
            wrapText('[u]', '[/u]', messageElement);
        } else {
            AddText('[u]', '[/u]', ' ', messageElement);
        }
    } else {
        if (hasSelection(messageElement)) {
            text = prompt(bbcode_prompt_underline, fetchSelection(messageElement));
            if (text != '' && text != undefined) {
                if (text == fetchSelection(messageElement)) {
                    wrapText('[u]', '[/u]', messageElement);
                } else {
                    AddText('[u]', '[/u]', text, messageElement);
                }
            }
        } else {
            text = prompt(bbcode_prompt_underline, 'Text');
            if (text != '' && text != undefined) {
                AddText('[u]', '[/u]', text, messageElement);
            }
        }
    }
}

function center() {
    if (helpmode) {
        alert(bbcode_help_center);
    } else if (advmode) {
        if (hasSelection(messageElement)) {
            wrapText('[align=center]', '[/align]', messageElement);
        } else {
            AddText('[align=center]', '[/align]', ' ', messageElement);
        }
    } else {
        if (hasSelection(messageElement)) {
            text = prompt(bbcode_prompt_center, fetchSelection(messageElement));
            if (text != '' && text != undefined) {
                if (text == fetchSelection(messageElement)) {
                    wrapText('[align=center]', '[/align]', messageElement);
                } else {
                    AddText('[align=center]', '[/align]', text, messageElement);
                }
            }
        } else {
            text = prompt(bbcode_prompt_center, 'Text');
            if (text != '' && text != undefined) {
                AddText('[align=center]', '[/align]', text, messageElement);
            }
        }
    }
}

function image() {
    if (helpmode) {
        alert(bbcode_help_image);
    } else if (advmode) {
        if (hasSelection(messageElement)) {
            wrapText('[img]', '[/img]', messageElement);
        } else {
            AddText('[img]', '[/img]', ' ', messageElement);
        }
    } else {
        if (hasSelection(messageElement)) {
            text = prompt(bbcode_prompt_image, fetchSelection(messageElement));
            if (text != '' && text != undefined) {
                if (text == fetchSelection(messageElement)) {
                    wrapText('[img]', '[/img]', messageElement);
                } else {
                    AddText('[img]', '[/img]', text, messageElement);
                }
            }
        } else {
            text = prompt(bbcode_prompt_image, 'http://www.example.com/image.jpg');
            if (text != '' && text != undefined) {
                AddText('[img]', '[/img]', text, messageElement);
            }
        }
    }
}

function quote() {
    if (helpmode) {
        alert(bbcode_help_quote);
    } else if (advmode) {
        if (hasSelection(messageElement)) {
            wrapText("\r\n"+'[quote]'+"\r\n", '[/quote]'+"\r\n", messageElement);
        } else {
            AddText("\r\n"+'[quote]'+"\r\n", '[/quote]'+"\r\n", ' ', messageElement);
        }
    } else {
        if (hasSelection(messageElement)) {
            text = prompt(bbcode_prompt_quote, fetchSelection(messageElement));
            if (text != '' && text != undefined) {
                if (text == fetchSelection(messageElement)) {
                    wrapText("\r\n"+'[quote]'+"\r\n", '[/quote]'+"\r\n", messageElement);
                } else {
                    AddText("\r\n"+'[quote]'+"\r\n", '[/quote]'+"\r\n", text, messageElement);
                }
            }
        } else {
            text = prompt(bbcode_prompt_quote, 'Text');
            if (text != '' && text != undefined) {
                AddText("\r\n"+'[quote]'+"\r\n", '[/quote]'+"\r\n", text, messageElement);
            }
        }
    }
}

function code() {
    if (helpmode) {
        alert(bbcode_help_code);
    } else if (advmode) {
        if (hasSelection(messageElement)) {
            wrapText("\r\n"+'[code]', '[/code]'+"\r\n", messageElement);
        } else {
            AddText("\r\n"+'[code]', '[/code]'+"\r\n", ' ', messageElement);
        }
    } else {
        if (hasSelection(messageElement)) {
            text = prompt(bbcode_prompt_code, fetchSelection(messageElement));
            if (text != '' && text != undefined) {
                if (text == fetchSelection(messageElement)) {
                    wrapText("\r\n"+'[code]', '[/code]'+"\r\n", messageElement);
                } else {
                    AddText("\r\n"+'[code]', '[/code]'+"\r\n", text, messageElement);
                }
            }
        } else {
            text = prompt(bbcode_prompt_code, 'Text');
            if (text != '' && text != undefined) {
                AddText("\r\n"+'[code]', '[/code]'+"\r\n", text, messageElement);
            }
        }
    }
}

function hyperlink() {
    if (helpmode) {
        alert(bbcode_help_link);
    } else if (advmode) {
        if (hasSelection(messageElement)) {
            if (fetchSelection(messageElement).match(URL_REGEXP_GOES_HERE) != undefined) {
                wrapText('[url]', '[/url]', messageElement);
            } else {
                wrapText('[url=', '] [/url]', messageElement);
            }
        } else {
            AddText('[url]', '[/url]', ' ', messageElement);
        }
    } else {
        if (hasSelection(messageElement)) {
            if (fetchSelection(messageElement).match(URL_REGEXP_GOES_HERE) != undefined) {
                var url = prompt(bbcode_prompt_link2, fetchSelection(messageElement));
                if (url != undefined) {
                    while (url.length == 0 || url.match(URL_REGEXP_GOES_HERE) == undefined) {
                        url = prompt(bbcode_prompt_link_url_error , fetchSelection(messageElement));
                    }
                }
                if (url != undefined) {
                    var desc = prompt(bbcode_prompt_link_desc, '');
                    if (desc != undefined) {
                        if (desc.length == 0) {
                            if (url == fetchSelection(messageElement)) {
                                wrapText('[url]', '[/url]', messageElement);
                            } else {
                                AddText('[url]', '[/url]', url, messageElement);
                            }
                        } else {
                            if (url == fetchSelection(messageElement)) {
                                wrapText('[url=', ']'+desc+'[/url]', messageElement);
                            } else {
                               AddText('[url='+url+']', '[/url]', desc, messageElement);
                            }
                        }
                    }
                }
            } else {
                var url = prompt(bbcode_prompt_link2, 'http://www.example.com');
                if (url != undefined) {
                    while (url.length == 0 || url.match(URL_REGEXP_GOES_HERE) == undefined) {
                        url = prompt(bbcode_prompt_link_url_error, url);
                    }
                }
                if (url != undefined) {
                    var desc = prompt(bbcode_prompt_link_desc, fetchSelection(messageElement));
                    if (desc != undefined) {
                        if (desc == fetchSelection(messageElement)) {
                            wrapText('[url='+url+']', '[/url]', messageElement);
                        } else {
                            AddText('[url='+url+']', '[/url]', desc, messageElement);
                        }
                    }
                }
            }
        } else {
            var url = prompt(bbcode_prompt_link2, 'http://www.example.com');
            if (url != undefined) {
                while (url.length == 0 || url.match(URL_REGEXP_GOES_HERE) == undefined) {
                    url = prompt(bbcode_prompt_link_url_error, url);
                }
            }

            if (url != undefined) {
                var desc = prompt(bbcode_prompt_link_desc, '');
                if (desc != undefined) {
                    if (desc.length > 0) {
                        AddText('[url='+url+']', '[/url]', desc, messageElement);
                    } else {
                        AddText('[url]', '[/url]', url, messageElement);
                    }
                }
            }
        }
    }
}

function list() {
    if (helpmode) {
        alert(bbcode_help_list);
    } else if (advmode) {
        if (hasSelection(messageElement)) {
            var selection = fetchSelection(messageElement);
            var listReg = new RegExp('(?:^|\r|\n)([^\r\n]+)(?=\r|\n|$)', 'g');
            var result;
            var returnStr = '';
            while (undefined != (result = listReg.exec(selection))) {
                returnStr += '[*]'+result[1]+"\r\n";
            }
            AddText('[list]', '[/list]', returnStr, messageElement);
        } else {
            AddText('[list]', '[/list]', '[*]'+"\r\n"+'[*]'+"\r\n"+'[*]'+"\r\n", messageElement);
        }
    } else {
        if (hasSelection(messageElement)) {
            var type = prompt(bbcode_prompt_list_start, '');
            if (type != undefined) {
                var cType = type.toLowerCase();
                while (cType != '' && cType != 'a' && cType != '1' && cType != undefined) {
                    type = prompt(bbcode_prompt_list_error, type);
                }
                var selection = fetchSelection(messageElement);
                var listReg = new RegExp('(?:^|\r|\n)([^\r\n]+)(?=\r|\n|$)', 'g');
                var result;
                var returnStr = '';
                var endStr = '[list'+((type == '' || type == undefined) ? ']' : '='+type+']');
                while (undefined != (result = listReg.exec(selection))) {
                    returnStr = prompt(bbcode_prompt_list_item+bbcode_prompt_list_end, result[1]);
                    if (returnStr != undefined) {
                        if (returnStr != result[1] && returnStr != '') {
                            while (returnStr != result[1] && returnStr != '' && returnStr != undefined) {
                                endStr += '[*]'+returnStr+"\r\n";
                                returnStr = prompt(bbcode_prompt_list_item+bbcode_prompt_list_end, result[1]);
                                if (returnStr != undefined) {
                                    if (returnStr == '') {
                                        break;
                                    } else {
                                        endStr += '[*]'+returnStr+"\r\n";
                                    }
                                }
                            }
                        } else if (returnStr == '') {
                            break;
                        } else {
                            endStr += '[*]'+returnStr+"\r\n";
                        }
                    }
                }
                if (result == undefined) {
                    while ('' != (returnStr = prompt(bbcode_prompt_list_end, ''))) {
                        endStr += '[*]'+returnStr+"\r\n";
                    }
                }
                endStr += '[/list'+((type == '' || type == undefined) ? ']' : '='+type+']');
                AddText('', '', endStr, messageElement);
            }
        } else {
            var returnStr = '';
            var type = prompt(bbcode_prompt_list_start, '');
            if (type != undefined) {
                var cType = type.toLowerCase();
                while (cType != '' && cType != 'a' && cType != '1' && cType != undefined) {
                    type = prompt(bbcode_prompt_list_error, type);
                    var cType = type.toLowerCase();
                }
                var endStr = '[list'+((type == '' || type == undefined) ? ']' : '='+type+']');
                while (undefined != (returnStr = prompt(bbcode_prompt_list_end, '')) && returnStr != '') {
                    endStr += '[*]'+returnStr+"\r\n";
                }
                endStr += '[/list'+((type == '' || type == undefined) ? ']' : '='+type+']');

                AddText('', '', endStr, messageElement);
            }
        }
    }
}

function storeCaret(textEl) {
    return undefined;
}
