<?php

// phpcs:disable PSR1.Files.SideEffects
namespace GaiaBB;

require_once ROOT . 'helper/formHelper.php';

class AdminThemeView
{
    public function __construct()
    {

    }

    public function displayThemePanel()
    {
        global $db, $lang, $THEME, $oToken, $CONFIG, $cheHTML, $shadow2, $theme;
    
        ?>
        <form method="post" action="cp_themes.php?action=updateallthemes" name="theme_main">
            <input type="hidden" name="csrf_token"
                   value="<?php echo $oToken->createToken() ?>"/>
            <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
                   align="center">
                <tr>
                    <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                        <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                               cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                            <tr class="category">
                                <td class="title" align="center"><?php echo $lang['textdeleteques'] ?></td>
                                <td class="title" align="center"><?php echo $lang['textthemename'] ?></td>
                                <td class="title" align="center"><?php echo $lang['numberusing'] ?></td>
                                <td class="title" align="center"><?php echo $lang['status'] ?></td>
                            </tr>
                            <?php
                            // altered theme code to produce a 20x speed increase
                            $themeMem = array(0 => 0);
                            $tq = $db->query("SELECT theme, COUNT(theme) as cnt FROM " .
                                X_PREFIX . "members GROUP BY theme");
                            while (($t = $db->fetchArray($tq)) != false) {
                                $themeMem[((int) $t['theme'])] = $t['cnt'];
                            }
                            $db->freeResult($tq);
    
                            $query = $db->query("SELECT name, themestatus, themeid FROM " .
                                        X_PREFIX . "themes ORDER BY name ASC");
                            while (($themeinfo = $db->fetchArray($query)) != false) {
                                $themeid = $themeinfo['themeid'];
                                if (!isset($themeMem[$themeid])) {
                                    $themeMem[$themeid] = 0;
                                }
    
                                if ($themeinfo['themeid'] == $CONFIG['theme']) {
                                    $members = ($themeMem[$themeid] + $themeMem[0]);
                                } else {
                                    $members = $themeMem[$themeid];
                                }
    
                                if ($themeinfo['themeid'] == $theme) {
                                    // XXX: figure out if $theme is from a parameter
                                    $checked = $cheHTML;
                                } else {
                                    $checked = 'checked="unchecked"';
                                }
                                ?>
                                <tr>
                                    <td bgcolor="<?php echo $THEME['altbg1'] ?>" class="ctrtablerow">
                                    <input type="checkbox" name="theme_delete[]" value="<?php echo $themeinfo['themeid'] ?>"/></td>
                                    <td bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                    <input type="text" name="theme_name[<?php echo $themeinfo['themeid'] ?>]" value="<?php echo $themeinfo['name'] ?>"/>
                                        &nbsp; [ 
                                    <a href="./cp_themes.php?action=displaytheme&amp;themeId=<?php echo $themeinfo['themeid'] ?>">
                                            <?php echo $lang['textdetails'] ?>
                                        </a>]
                                        &nbsp;-&nbsp;
                                        [ 
                                <a href="./cp_themes.php?action=downloadtheme&amp;themeId=<?php echo $themeinfo['themeid'] ?>">
                                            <?php echo $lang['textdownload'] ?>
                                        </a>
                                        ]
                                    </td>
                                    <td bgcolor="<?php echo $THEME['altbg1'] ?>"
                                        class="ctrtablerow"><?php echo $members ?></td>
                                    <td bgcolor="<?php echo $THEME['altbg2'] ?>"
                                        class="ctrtablerow"><?php echo $themeinfo['themestatus'] ?></td>
                                </tr>
                                <?php
                            }
                                $db->freeResult($query);
                            ?>
                            <tr bgcolor="<?php echo $THEME['altbg1'] ?>" class="tablerow">
                                <td colspan="4">
                                    <a href="./cp_themes.php?action=newtheme">
                                        <strong>
                                            <?php echo $lang['textnewtheme'] ?>
                                        </strong>
                                    </a>
                                    - <a href="#" 
                                        onclick="setCheckboxes('theme_main', 'theme_delete[]', true); return false;">
                                            <?php echo $lang['checkall'] ?>
                                      </a>
                                    - <a href="#"
                                         onclick="setCheckboxes('theme_main', 'theme_delete[]', false); return false;">
                                             <?php echo $lang['uncheckall'] ?>
                                        </a>
                                    - <a href="#"
                                         onclick="invertSelection('theme_main', 'theme_delete[]'); return false;">
                                         <?php echo $lang['invertselection'] ?>
                                    </a>
                                </td>
                            </tr>
                            <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                                <td colspan="4">
                                    <input type="submit" name="themesubmit" value="<?php echo $lang['textsubmitchanges'] ?>" class="submit"/>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <?php echo $shadow2 ?>
        </form>
        <br/>
        <form method="post" action="cp_themes.php?action=importtheme"
              enctype="multipart/form-data">
            <input type="hidden" name="csrf_token"
                   value="<?php echo $oToken->createToken() ?> "/>
            <table cellspacing="0px" cellpadding="0px" border="0px" width="100%"
                   align="center">
                <tr>
                    <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                        <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>"
                               cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                            <tr class="category">
                                <td colspan="2" class="title"><?php echo $lang['textimporttheme'] ?></td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textthemefile'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>"><input type="file"
                                                                                    name="themefile" value="" size="40"/>
                                </td>
                            </tr>
                            <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                                <td colspan="2"><input type="submit" class="submit"
                                                       name="importsubmit"
                                                       value="<?php echo $lang['textimportsubmit'] ?>"/></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <?php echo $shadow2 ?>
        </form>
        </td>
        </tr>
        </table>
        <?php
    }
    
    public function displaySingleTheme()
    {
        global $db, $oToken, $selHTML, $THEME, $lang, $shadow2;

        $themeId = getInt('themeId', 0);
        $query = $db->query("SELECT * FROM " . X_PREFIX . "themes WHERE themeid = '$themeId'");
        $themedata = $db->fetchArray($query);
        $db->freeResult($query);

        if ($themedata === false or empty($themedata)) {
            cp_error($lang['nosuchthemeid'], false, '', '</td></tr></table>', 'cp_themes.php');
        }

        ?>
        <pre>
        <!--
        Themedata: [
        <?php
            print_r($themedata);
        ?> 
        ]
        -->
        </pre>
        <?php

        $roundon = $squareon = $none = '';
        switch ($themedata['outertable']) {
            case 'round':
                $roundon = $selHTML;
                break;
            case 'square':
                $squareon = $selHTML;
                break;
            default:
                $none = $selHTML;
                break;
        }

        $threadoptimg = $threadopttxt = '';
        switch ($themedata['threadopts']) {
            case 'image':
                $threadoptimg = $selHTML;
                break;
            default:
                $threadopttxt = $selHTML;
                break;
        }

        $shadowon = $shadowoff = '';
        FormHelper::getThemeOnOffHtml('shadowfx', $shadowon, $shadowoff);
        $themeon = $themeoff = '';
        FormHelper::getThemeOnOffHtml('themestatus', $themeon, $themeoff);
        $celloveron = $celloveroff = '';
        FormHelper::getThemeOnOffHtml('celloverfx', $celloveron, $celloveroff);
        $riconon = $riconoff = '';
        FormHelper::getThemeOnOffHtml('riconstatus', $riconon, $riconoff);
        $spacecatson = $spacecatsoff = '';
        FormHelper::getThemeOnOffHtml('space_cats', $spacecatson, $spacecatsoff);

        $themedata['name'] = stripslashes($themedata['name']);
        $themedata['bgcolor'] = stripslashes($themedata['bgcolor']);
        $themedata['altbg1'] = stripslashes($themedata['altbg1']);
        $themedata['altbg2'] = stripslashes($themedata['altbg2']);
        $themedata['link'] = stripslashes($themedata['link']);
        $themedata['bordercolor'] = stripslashes($themedata['bordercolor']);
        $themedata['header'] = stripslashes($themedata['header']);
        $themedata['headertext'] = stripslashes($themedata['headertext']);
        $themedata['top'] = stripslashes($themedata['top']);
        $themedata['catcolor'] = stripslashes($themedata['catcolor']);
        $themedata['tabletext'] = stripslashes($themedata['tabletext']);
        $themedata['text'] = stripslashes($themedata['text']);
        $themedata['borderwidth'] = stripslashes($themedata['borderwidth']);
        $themedata['tablewidth'] = stripslashes($themedata['tablewidth']);
        $themedata['tablespace'] = stripslashes($themedata['tablespace']);
        $themedata['fontsize'] = stripslashes($themedata['fontsize']);
        $themedata['font'] = stripslashes($themedata['font']);
        $themedata['boardimg'] = stripslashes($themedata['boardimg']);
        $themedata['imgdir'] = stripslashes($themedata['imgdir']);
        $themedata['smdir'] = stripslashes($themedata['smdir']);
        $themedata['cattext'] = stripslashes($themedata['cattext']);
        $themedata['outerbgcolor'] = stripslashes($themedata['outerbgcolor']);
        $themedata['outertable'] = stripslashes($themedata['outertable']);
        $themedata['outertablewidth'] = stripslashes($themedata['outertablewidth']);
        $themedata['outerbordercolor'] = stripslashes($themedata['outerbordercolor']);
        $themedata['outerborderwidth'] = stripslashes($themedata['outerborderwidth']);
        $themedata['navsymbol'] = stripslashes($themedata['navsymbol']);
        $themedata['spacolor'] = stripslashes($themedata['spacolor']);
        $themedata['admcolor'] = stripslashes($themedata['admcolor']);
        $themedata['spmcolor'] = stripslashes($themedata['spmcolor']);
        $themedata['modcolor'] = stripslashes($themedata['modcolor']);
        $themedata['memcolor'] = stripslashes($themedata['memcolor']);
        $themedata['ricondir'] = stripslashes($themedata['ricondir']);
        $themedata['highlight'] = stripslashes($themedata['highlight']);

        if (false === strpos($themedata['catcolor'], '.')) {
            $catcode = 'style="background-color: ' . $themedata['catcolor'] . '"';
        } else {
            $catcode = 'style="background-image: url(../' . $themedata['imgdir'] . '/' . $themedata['catcolor'] . ')"';
        }
        if (false === strpos($themedata['top'], '.')) {
            $topcode = 'style="background-color: ' . $themedata['top'] . '"';
        } else {
            $topcode = 'style="background-image: url(../' . $themedata['imgdir'] . '/' . $themedata['top'] . ')"';
        }
        ?>
        <form method="post" action="cp_themes.php?action=updatesingletheme">
            <input type="hidden" name="csrf_token" value="<?php echo $oToken->createToken() ?>" />
            <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
                <tr>
                    <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                        <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>" cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                            <tr class="category">
                                <td class="title" colspan="3"><?php echo $lang['Edit_Theme'] ?></td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['Theme_Status'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><select name="themestatusnew">
                                        <option value="on" <?php echo $themeon ?>>
                                            <?php echo $lang['texton'] ?></option>
                                        <option value="off" <?php echo $themeoff ?>>
                                            <?php echo $lang['textoff'] ?></option>
                                    </select></td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['space_cats'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><select name="space_catsnew">
                                        <option value="on" <?php echo $spacecatson ?>>
                                            <?php echo $lang['texton'] ?>
                                        </option>
                                        <option value="off" <?php echo $spacecatsoff ?>>
                                            <?php echo $lang['textoff'] ?>
                                        </option>
                                    </select></td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['tableshadoweffects'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><select name="shadowfxnew">
                                        <option value="on" <?php echo $shadowon ?>><?php echo $lang['texton'] ?></option>
                                        <option value="off" <?php echo $shadowoff ?>><?php echo $lang['textoff'] ?></option>
                                    </select></td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['themecell'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><select name="celloverfxnew">
                                        <option value="on" <?php echo $celloveron ?>><?php echo $lang['texton'] ?>
                                        </option>
                                        <option value="off" <?php echo $celloveroff ?>><?php echo $lang['textoff'] ?>
                                        </option>
                                    </select></td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['riconstatus'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2">
                                    <select name="riconstatusnew">
                                        <option value="on" <?php echo $riconon ?>><?php echo $lang['texton'] ?></option>
                                        <option value="off" <?php echo $riconoff ?>><?php echo $lang['textoff'] ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['outertable'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2">
                                    <select name="outertablenew">
                                        <option value="none" <?php echo $none ?>><?php echo $lang['none'] ?></option>
                                        <option value="round" <?php echo $roundon ?>><?php echo $lang['round'] ?></option>
                                        <option value="square" <?php echo $squareon ?>><?php echo $lang['square'] ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['threadoptstatus'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2">
                                    <select name="threadoptsnew">
                                        <option value="text" <?php echo $threadopttxt ?>>
                                            <?php echo $lang['threadopttext'] ?></option>
                                        <option value="image" <?php echo $threadoptimg ?>>
                                            <?php echo $lang['threadoptimage'] ?></option>
                                    </select></td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['texthemename'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input type="text" name="namenew" value="<?php echo $themedata['name'] ?>" /></td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['navsymbol'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2">
                                    <input type="text" name="navsymbolnew" value="<?php echo $themedata['navsymbol'] ?>" />
                                </td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textbgcolor'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>">
                                    <input type="text" name="bgcolornew" value="<?php echo $themedata['bgcolor'] ?>" />
                                </td>
                                <td bgcolor="<?php echo $themedata['bgcolor'] ?>">&nbsp;</td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['outerbgcolor'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>">
                                    <input type="text" name="outerbgcolornew" value="<?php echo $themedata['outerbgcolor'] ?>" />
                                </td>
                                <td bgcolor="<?php echo $themedata['outerbgcolor'] ?>">&nbsp;</td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textaltbg1'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>">
                                    <input type="text" name="altbg1new" value="<?php echo $themedata['altbg1'] ?>" />
                                </td>
                                <td bgcolor="<?php echo $themedata['altbg1'] ?>">&nbsp;</td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textaltbg2'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>">
                                    <input type="text" name="altbg2new" value="<?php echo $themedata['altbg2'] ?>" />
                                </td>
                                <td bgcolor="<?php echo $themedata['altbg2'] ?>">&nbsp;</td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['highlight'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>">
                                    <input type="text" name="highlightnew" value="<?php echo $themedata['highlight'] ?>" />
                                </td>
                                <td bgcolor="<?php echo $themedata['highlight'] ?>">&nbsp;</td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['spacolor'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>">
                                    <input type="text" name="spacolornew" value="<?php echo $themedata['spacolor'] ?>" />
                                </td>
                                <td bgcolor="<?php echo $themedata['spacolor'] ?>">&nbsp;</td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['admcolor'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>">
                                    <input type="text" name="admcolornew" value="<?php echo $themedata['admcolor'] ?>" />
                                </td>
                                <td bgcolor="<?php echo $themedata['admcolor'] ?>">&nbsp;</td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['spmcolor'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>">
                                    <input type="text" name="spmcolornew" value="<?php echo $themedata['spmcolor'] ?>" />
                                </td>
                                <td bgcolor="<?php echo $themedata['spmcolor'] ?>">&nbsp;</td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['modcolor'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>">
                                    <input type="text" name="modcolornew" value="<?php echo $themedata['modcolor'] ?>" />
                                </td>
                                <td bgcolor="<?php echo $themedata['modcolor'] ?>">&nbsp;</td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['memcolor'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>">
                                    <input type="text" name="memcolornew" value="<?php echo $themedata['memcolor'] ?>" />
                                </td>
                                <td bgcolor="<?php echo $themedata['memcolor'] ?>">&nbsp;</td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textlink'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>">
                                    <input type="text" name="linknew" value="<?php echo $themedata['link'] ?>" />
                                </td>
                                <td bgcolor="<?php echo $themedata['link'] ?>">&nbsp;</td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textborder'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>">
                                    <input type="text" name="bordercolornew" value="<?php echo $themedata['bordercolor'] ?>" />
                                </td>
                                <td bgcolor="<?php echo $themedata['bordercolor'] ?>">&nbsp;</td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['outerbordercolor'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>">
                                    <input type="text" name="outerbordercolornew" value="<?php echo $themedata['outerbordercolor'] ?>" />
                                </td>
                                <td bgcolor="<?php echo $themedata['outerbordercolor'] ?>">&nbsp;</td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textheader'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>">
                                    <input type="text" name="headernew" value="<?php echo $themedata['header'] ?>" />
                                </td>
                                <td bgcolor="<?php echo $themedata['header'] ?>">&nbsp;</td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textheadertext'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>">
                                    <input type="text" name="headertextnew" value="<?php echo $themedata['headertext'] ?>" />
                                </td>
                                <td bgcolor="<?php echo $themedata['headertext'] ?>">&nbsp;</td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['texttop'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>">
                                    <input type="text" name="topnew" value="<?php echo $themedata['top'] ?>" />
                                </td>
                                <td <?php echo $topcode ?>>&nbsp;</td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textcatcolor'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>">
                                    <input type="text" name="catcolornew" value="<?php echo $themedata['catcolor'] ?>" />
                                </td>
                                <td <?php echo $catcode ?>>&nbsp;</td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textcattextcolor'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>">
                                    <input type="text" name="cattextnew" value="<?php echo $themedata['cattext'] ?>" />
                                </td>
                                <td bgcolor="<?php echo $themedata['cattext'] ?>">&nbsp;</td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['texttabletext'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>">
                                    <input type="text" name="tabletextnew" value="<?php echo $themedata['tabletext'] ?>" />
                                </td>
                                <td bgcolor="<?php echo $themedata['tabletext'] ?>">&nbsp;</td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['texttext'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>">
                                    <input type="text" name="textnew" value="<?php echo $themedata['text'] ?>" />
                                </td>
                                <td bgcolor="<?php echo $themedata['text'] ?>">&nbsp;</td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textborderwidth'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2">
                                    <input type="text" name="borderwidthnew" value="<?php echo $themedata['borderwidth'] ?>" size="2" />
                                </td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['outerborderwidth'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input type="text" name="outerborderwidthnew" value="<?php echo $themedata['outerborderwidth'] ?>" size="2" /></td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textwidth'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input type="text" name="tablewidthnew" value="<?php echo $themedata['tablewidth'] ?>" size="3" /></td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['outertablewidth'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input type="text" name="outertablewidthnew" value="<?php echo $themedata['outertablewidth'] ?>" size="3" /></td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textspace'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input type="text" name="tablespacenew" value="<?php echo $themedata['tablespace'] ?>" size="2" /></td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textfont'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input type="text" name="fontnew" value="<?php echo htmlspecialchars($themedata['font']) ?>" /></td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textbigsize'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input type="text" name="fontsizenew" value="<?php echo $themedata['fontsize'] ?>" size="4" /></td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['textboardlogo'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input type="text" value="<?php echo $themedata['boardimg'] ?>" name="boardlogonew" /></td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['imgdir'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input type="text" value="<?php echo $themedata['imgdir'] ?>" name="imgdirnew" /></td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['smdir'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input type="text" value="<?php echo $themedata['smdir'] ?>" name="smdirnew" /></td>
                            </tr>
                            <tr class="tablerow">
                                <td bgcolor="<?php echo $THEME['altbg1'] ?>"><?php echo $lang['ricondir'] ?></td>
                                <td bgcolor="<?php echo $THEME['altbg2'] ?>" colspan="2"><input type="text" value="<?php echo $themedata['ricondir'] ?>" name="ricondirnew" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="ctrtablerow">
                                <td colspan="3"><input type="submit" class="submit" value="<?php echo $lang['textsubmitchanges'] ?>" /><input type="hidden" name="orig" value="<?php echo $themeId ?>" /></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <?php echo $shadow2 ?>
        </form>
        </td>
        </tr>
        </table>
        <?php
    }

    public function displayNewThemePanel()
    {
        global $THEME, $oToken, $lang, $shadow2;
        ?>
        <form method="post" action="cp_themes.php?action=createtheme">
            <input type="hidden" name="csrf_token" value="<?php echo $oToken->createToken() ?>" />
            <table cellspacing="0px" cellpadding="0px" border="0px" width="100%" align="center">
                <tr>
                    <td bgcolor="<?php echo $THEME['bordercolor'] ?>">
                        <table border="0px" cellspacing="<?php echo $THEME['borderwidth'] ?>" cellpadding="<?php echo $THEME['tablespace'] ?>" width="100%">
                            <tr class="category">
                                <td class="title" colspan="2"><?php echo $lang['textnewtheme'] ?></td>
                            </tr>
                            <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                                <td><?php echo $lang['Theme_Status'] ?></td>
                                <td colspan="2"><select name="themestatusnew">
                                        <option value="on"><?php echo $lang['texton'] ?></option>
                                        <option value="off"><?php echo $lang['textoff'] ?></option>
                                    </select></td>
                            </tr>
                            <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                                <td><?php echo $lang['space_cats'] ?></td>
                                <td colspan="2"><select name="space_catsnew">
                                        <option value="on"><?php echo $lang['texton'] ?></option>
                                        <option value="off"><?php echo $lang['textoff'] ?></option>
                                    </select></td>
                            </tr>
                            <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                                <td><?php echo $lang['tableshadoweffects'] ?></td>
                                <td colspan="2"><select name="shadowfxnew">
                                        <option value="on"><?php echo $lang['texton'] ?></option>
                                        <option value="off"><?php echo $lang['textoff'] ?></option>
                                    </select></td>
                            </tr>
                            <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                                <td><?php echo $lang['themecell'] ?></td>
                                <td colspan="2"><select name="celloverfxnew">
                                        <option value="on"><?php echo $lang['texton'] ?></option>
                                        <option value="off"><?php echo $lang['textoff'] ?></option>
                                    </select></td>
                            </tr>
                            <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                                <td><?php echo $lang['riconstatus'] ?></td>
                                <td colspan="2"><select name="riconstatusnew">
                                        <option value="on"><?php echo $lang['texton'] ?></option>
                                        <option value="off"><?php echo $lang['textoff'] ?></option>
                                    </select></td>
                            </tr>
                            <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                                <td><?php echo $lang['outertable'] ?></td>
                                <td colspan="2"><select name="outertablenew">
                                        <option value="none"><?php echo $lang['none'] ?></option>
                                        <option value="round"><?php echo $lang['round'] ?></option>
                                        <option value="square"><?php echo $lang['square'] ?></option>
                                    </select></td>
                            </tr>
                            <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                                <td><?php echo $lang['threadoptstatus'] ?></td>
                                <td colspan="2"><select name="threadoptsnew">
                                        <option value="text"><?php echo $lang['threadopttext'] ?></option>
                                        <option value="image"><?php echo $lang['threadoptimage'] ?></option>
                                    </select></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['texthemename'] ?></td>
                                <td><input type="text" name="namenew" value="" /></td>
                            </tr>
                            <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                                <td><?php echo $lang['navsymbol'] ?></td>
                                <td><input type="text" name="navsymbolnew" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['textbgcolor'] ?></td>
                                <td><input type="text" name="bgcolornew" value="" /></td>
                            </tr>
                            <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                                <td><?php echo $lang['outerbgcolor'] ?></td>
                                <td><input type="text" name="outerbgcolornew" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['textaltbg1'] ?></td>
                                <td><input type="text" name="altbg1new" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['textaltbg2'] ?></td>
                                <td><input type="text" name="altbg2new" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['highlight'] ?></td>
                                <td><input type="text" name="highlightnew" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['spacolor'] ?></td>
                                <td><input type="text" name="spacolornew" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['admcolor'] ?></td>
                                <td><input type="text" name="admcolornew" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['spmcolor'] ?></td>
                                <td><input type="text" name="spmcolornew" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['modcolor'] ?></td>
                                <td><input type="text" name="modcolornew" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['memcolor'] ?></td>
                                <td><input type="text" name="memcolornew" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['textlink'] ?></td>
                                <td><input type="text" name="linknew" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['textborder'] ?></td>
                                <td><input type="text" name="bordercolornew" value="" /></td>
                            </tr>
                            <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                                <td><?php echo $lang['outerbordercolor'] ?></td>
                                <td><input type="text" name="outerbordercolornew" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['textheader'] ?></td>
                                <td><input type="text" name="headernew" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['textheadertext'] ?></td>
                                <td><input type="text" name="headertextnew" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['texttop'] ?></td>
                                <td><input type="text" name="topnew" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['textcatcolor'] ?></td>
                                <td><input type="text" name="catcolornew" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['textcattextcolor'] ?></td>
                                <td><input type="text" name="cattextnew" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['texttabletext'] ?></td>
                                <td><input type="text" name="tabletextnew" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['texttext'] ?></td>
                                <td><input type="text" name="textnew" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['textborderwidth'] ?></td>
                                <td><input type="text" name="borderwidthnew" size="2" value="" /></td>
                            </tr>
                            <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                                <td><?php echo $lang['outerborderwidth'] ?></td>
                                <td><input type="text" name="outerborderwidthnew" size="2" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['textwidth'] ?></td>
                                <td><input type="text" name="tablewidthnew" size="3" value="" /></td>
                            </tr>
                            <tr class="tablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                                <td><?php echo $lang['outertablewidth'] ?></td>
                                <td><input type="text" name="outertablewidthnew" size="3" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['textspace'] ?></td>
                                <td><input type="text" name="tablespacenew" size="2" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['textfont'] ?></td>
                                <td><input type="text" name="fontnew" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['textbigsize'] ?></td>
                                <td><input type="text" name="fontsizenew" size="4" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['textboardlogo'] ?></td>
                                <td><input type="text" name="boardlogonew" value="" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['imgdir'] ?></td>
                                <td><input type="text" name="imgdirnew" value="images/" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['smdir'] ?></td>
                                <td><input type="text" name="smdirnew" value="images/smilies" /></td>
                            </tr>
                            <tr bgcolor="<?php echo $THEME['altbg2'] ?>" class="tablerow">
                                <td><?php echo $lang['ricondir'] ?></td>
                                <td><input type="text" name="ricondirnew" value="images/ricons" /></td>
                            </tr>
                            <tr class="ctrtablerow" bgcolor="<?php echo $THEME['altbg2'] ?>">
                                <td colspan="2"><input class="submit" type="submit" value="<?php echo $lang['textsubmitchanges'] ?>" /> <input type="hidden" name="newtheme" value="<?php echo $themeId ?>" /></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <?php echo $shadow2 ?>
        </form>
        </td>
        </tr>
        </table>
        <?php
    }
}
