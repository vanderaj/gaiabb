<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2014 The GaiaBB Group
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

if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false))
{
    exit('This file is not designed to be called directly');
}

class captcha
{
    public $site_tags = GAIABB_VERSION;
    public $tag_pos = 'top'; // top || bottom;
    public $max_word_length = 7;
    public $min_word_length = 5;
    public $max_attempts = 200;
    public $im;
    public $im2;
    public $im3;
    public $word;
    public $font_pixelwidth;
    public $font_widths;
    public $width;
    public $height;
    public $bg_fade_pct;
    public $bg;
    public $morph_bg = false;
    public $font_locations = array("include/captcha/mpl1.gdf", "include/captcha/mpl2.gdf", "include/captcha/mpl3.gdf");
    public $color_management = 'single'; // single || multiple
    public $merge_type = 1;
    public $global_col_r;
    public $global_col_b;
    public $global_col_g;
    public $bg2;
    public $word_start_x;
    public $word_start_y;

    function captcha()
    {
        global $CONFIG;

        if (!empty($CONFIG))
        {
            $this->max_word_length = $CONFIG['captcha_maxchars'];
            $this->min_word_length = $CONFIG['captcha_minchars'];
            $this->max_attempts = $CONFIG['captcha_maxattempts'];
            $this->color_management = $CONFIG['captcha_colortype'];

            define('CAPTCHA_FONT_PATH', $CONFIG['captcha_fontpath']);
            $this->font_locations = array(CAPTCHA_FONT_PATH."/mpl1.gdf", CAPTCHA_FONT_PATH."/mpl2.gdf", CAPTCHA_FONT_PATH."/mpl3.gdf");
        }

        mt_srand($this->make_seed());

        $this->bg_fade_pct = 40;

        if ($this->color_management == 'single')
        {
            $this->global_col_r = $this->rand_color();
            $this->global_col_b = $this->rand_color();
            $this->global_col_g = $this->rand_color();
        }

        $this->bg_fade_pct += mt_rand(-2,2);

        for ($i=0; $i<sizeof($this->font_locations); $i++)
        {
            $handle = fopen($this->font_locations[$i],"r");
            $c_wid = fread($handle,11);
            $this->font_widths[$i] = ord($c_wid{8})+ord($c_wid{9})+ord($c_wid{10});
            fclose($handle);
        }

        $this->width = ($this->max_word_length*(array_sum($this->font_widths)/sizeof($this->font_widths))+50);
        $this->height = 90;

        $this->im = ImageCreate($this->width, $this->height);
        $this->im2 = ImageCreate($this->width, $this->height);

        if ($CONFIG['captcha_maxattempts'] != '0')
        {
            $this->CheckBruteForce();
        }

        $this->GenerateWord();
        $this->FillBGColor();
        $this->WriteWord();
        $this->MorphImage();
        $this->AddSiteTags();
        $this->MergeBG();
        $this->SendImage();
    }

    function CheckBruteForce()
    {
        if (empty($_SESSION['captcha_attempts']))
        {
            $_SESSION['captcha_attempts'] = 1;
        }
        else
        {
            $_SESSION['captcha_attempts']++;

            if ($_SESSION['captcha_attempts'] > $this->max_attempts)
            {
                $_SESSION['word_hash'] = false;

                $this->bg = ImageColorAllocate($this->im,255,255,255);
                ImageColorTransparent($this->im,$this->bg);

                $red = ImageColorAllocate($this->im, 255, 0, 0);
                ImageString($this->im,5,15,20,"service no longer available",$red);

                $this->SendImage();
            }
        }
    }

    function make_seed()
    {
        list($usec, $sec) = explode(' ', microtime());
        return (float) $sec + ((float) $usec * 100000);
    }

    function rand_color()
    {
        return mt_rand(60,170);
    }

    function myImageBlur()
    {
        $temp_im = ImageCreate($this->width,$this->height);
        $this->bg = ImageColorAllocate($temp_im,150,150,150);

        ImageColorTransparent($temp_im,$this->bg);

        ImageFill($temp_im,0,0,$this->bg);

        $distance = 1;

        ImageCopyMerge($temp_im, $this->im, 0, 0, 0, $distance, $this->width, $this->height-$distance, 70);
        ImageCopyMerge($this->im, $temp_im, 0, 0, $distance, 0, $this->width-$distance, $this->height, 70);
        ImageCopyMerge($temp_im, $this->im, 0, $distance, 0, 0, $this->width, $this->height, 70);
        ImageCopyMerge($this->im, $temp_im, $distance, 0, 0, 0, $this->width, $this->height, 70);

        ImageDestroy($temp_im);

        return $this->im;
    }

    function SendImage()
    {
        header("Content-Type: image/png");
        ImagePNG($this->im);

        ImageDestroy($this->im);
        ImageDestroy($this->im2);
        if (!empty($this->im3))
        {
            ImageDestroy($this->im3);
        }
        exit;
    }

    function GenerateWord()
    {
        $consonants = 'bcdfghjkmnprstvwxyz';
        $mixed = 'aeu23456789';

        $wordlen = mt_rand($this->min_word_length,$this->max_word_length);

        for ($i=0 ; $i<$wordlen ; $i++)
        {
            if (mt_rand(0,4)>=2)
            {
                $this->word .= $consonants{mt_rand(0,strlen($consonants)-1)};
            }
            else
            {
                $this->word .= $mixed{mt_rand(0,strlen($mixed)-1)};
            }
        }
        $_SESSION['word_hash'] = md5(strtolower($this->word));
    }

    function FillBGColor()
    {
        $tag_col = ImageColorAllocate($this->im,10,10,10);

        $this->bg = ImageColorAllocate($this->im, 254, 254, 254);
        $this->bg2 = ImageColorAllocate($this->im2, 254, 254, 254);

        ImageColorTransparent($this->im,$this->bg);
        ImageColorTransparent($this->im2,$this->bg2);

        ImageFill($this->im,0,0,$this->bg);
        ImageFill($this->im2,0,0,$this->bg2);

        // Start BG ROUTINE (SQUIGGLE)
        $this->im3 = ImageCreate($this->width,$this->height);
        $temp_bg = ImageCreate($this->width*1.5,$this->height*1.5);
        $bg3 = ImageColorAllocate($this->im3,255,255,255);
        ImageFill($this->im3,0,0,$bg3);
        $temp_bg_col = ImageColorAllocate($temp_bg,255,255,255);
        ImageFill($temp_bg,0,0,$temp_bg_col);

        $bg3 = ImageColorAllocate($this->im3,255,255,255);
        ImageFill($this->im3,0,0,$bg3);
        ImageSetThickness($temp_bg,4);

        for ($i=0 ; $i<strlen($this->word)+1 ; $i++)
        {
            if ($this->color_management == 'single')
            {
                $text_r = $this->global_col_r;
                $text_g = $this->global_col_g;
                $text_b = $this->global_col_b;
            }
            else
            {
                $text_r = mt_rand(100,150);
                $text_b = mt_rand(100,150);
                $text_g = mt_rand(100,150);
            }

            $text_colour3 = ImageColorAllocate($temp_bg, $text_r,$text_b,$text_g);

            $points = array();
            for ($j=1 ; $j<mt_rand(5,9) ; $j++)
            {
                $points[] = mt_rand(1*(20*($i+1)),1*(50*($i+1)));
                $points[] = mt_rand(30,$this->height+30);
            }

            ImagePolygon($temp_bg,$points,intval(sizeof($points)/5),$text_colour3);
        }
        // End BG ROUTINE (SQUIGGLE)

        $morph_chunk = mt_rand(0,5);
        $morph_y = 0;
        for ($x=0 ; $x<$this->width ; $x+=$morph_chunk)
        {
            $morph_chunk = mt_rand(1,5);
            $morph_y += mt_rand(-1,1);
            ImageCopy($this->im3, $temp_bg, $x, 0, $x+30, 30+$morph_y, $morph_chunk, $this->height*2);
        }

        ImageCopy($temp_bg, $this->im3, 0, 0, 0, 0, $this->width, $this->height);

        $morph_x = 0;
        for ($y=0 ; $y<=$this->height; $y+=$morph_chunk)
        {
            $morph_chunk = mt_rand(1,5);
            $morph_x += mt_rand(-1,1);
            ImageCopy($this->im3, $temp_bg, $morph_x, $y, 0, $y, $this->width, $morph_chunk);
        }

        ImageDestroy($temp_bg);
    }

    function WriteWord()
    {
        $this->word_start_x = 20;
        $this->word_start_y = 25;

        if ($this->color_management == 'single')
        {
            $text_r = $this->global_col_r;
            $text_g = $this->global_col_g;
            $text_b = $this->global_col_b;
        }

        for ($i=0 ; $i<strlen($this->word) ; $i++)
        {
            if ($this->color_management != 'single')
            {
                $text_r = $this->rand_color();
                $text_g = $this->rand_color();
                $text_b = $this->rand_color();
            }

            $text_colour2 = ImageColorAllocate($this->im2, $text_r,$text_b,$text_g);

            $j = mt_rand(0,sizeof($this->font_locations)-1);
            $font = ImageLoadFont($this->font_locations[$j]);
            ImageString($this->im2, $font, $this->word_start_x+(25*$i), $this->word_start_y, $this->word{$i}, $text_colour2);
        }
        $this->font_pixelwidth = $this->font_widths[$j];
    }

    function MorphImage()
    {
        $word_pix_size = $this->word_start_x+(strlen($this->word)*$this->font_pixelwidth);

        for ($i=$this->word_start_x ; $i<$word_pix_size ; $i+=$this->font_pixelwidth)
        {
            ImageCopy($this->im, $this->im2, $i, 0, $i, 0, $this->font_pixelwidth, $this->height);
        }

        ImageFilledRectangle($this->im2,0,0,$this->width,$this->height,$this->bg2);

        $y_chunk = 1;
        $morph_x = 0;
        for ($j=0 ; $j<strlen($this->word) ; $j++)
        {
            for ($i=0 ; $i<=$this->height; $i+=$y_chunk)
            {
                $orig_x = $this->word_start_x+($j*$this->font_pixelwidth);
                ImageCopyMerge($this->im2, $this->im, $orig_x, $i, $orig_x, $i, $this->font_pixelwidth, $y_chunk, 100);
            }
        }

        ImageFilledRectangle($this->im,0,0,$this->width,$this->height,$this->bg);

        $y_pos = 0;
        $x_chunk = 1;
        for ($i=0; $i<=$this->width; $i+=$x_chunk)
        {
            ImageCopy($this->im, $this->im2, $i, 0, $i, 0, $x_chunk, $this->height);
        }
        $this->myImageBlur($this->im);
    }

    function AddSiteTags()
    {
        $site_tag_col2 = ImageColorAllocate($this->im2,0,0,0);
        ImageFilledRectangle($this->im2,0,0,$this->width,$this->height,$this->bg2);
        $tag_width = strlen($this->site_tags)*6;
        if ($this->tag_pos == 'top')
        {
            ImageString($this->im2, 2, intval($this->width/2)-intval($tag_width/2), 10, $this->site_tags, $site_tag_col2);
        }

        if ($this->tag_pos == 'bottom')
        {
            ImageString($this->im2, 2, intval($this->width/2)-intval($tag_width/2), ($this->height-24), $this->site_tags, $site_tag_col2);
        }
        ImageCopyMerge($this->im2,$this->im,0,0,0,0,$this->width,$this->height,80);
        ImageCopy($this->im,$this->im2,0,0,0,0,$this->width,$this->height);
    }

    function MergeBG()
    {
        $temp_im = ImageCreate($this->width,$this->height);
        $white = ImageColorAllocate($temp_im,255,255,255);
        ImageFill($temp_im,0,0,$white);
        ImageCopyMerge($this->im3,$temp_im,0,0,0,0,$this->width,$this->height,$this->bg_fade_pct);
        ImageDestroy($temp_im);
        $c_fade_pct = 50;

        if ($this->merge_type == 1)
        {
            ImageCopyMerge($this->im3,$this->im,0,0,0,0,$this->width,$this->height,100);
            ImageCopy($this->im,$this->im3,0,0,0,0,$this->width,$this->height);
        }
        else
        {
            ImageCopyMerge($this->im,$this->im3,0,0,0,0,$this->width,$this->height,$c_fade_pct);
        }
    }
}
?>