<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2013 The GaiaBB Group
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

class MailSys
{
    public $from;
    public $to;
    public $subject;
    public $message;
    public $headers;
    public $bcc;
    public $cc;
    public $smtp_conn;

    // Instantiate the class
    function MailSys()
    {
        $OS = substr(PHP_OS, 0, 3);
        define('GAIABB_OS', $OS);
    }

    // Required calls
    function setFrom($email, $name = '')
    {
        $email = trim($email);
        if (empty($email))
        {
            return FALSE;
        }

        $name = trim($name);
        if (!empty($name))
        {
            $this->from = $name.' <'.$email.'>';
        }
        else
        {
            $this->from = $email;
        }

        $this->addHeader('From', $this->from);
        $this->addHeader('Reply-To', $email);
        return TRUE;
    }

    function setTo($email)
    {
        $email = trim($email);
        if (empty($email))
        {
            return FALSE;
        }
        else
        {
            $this->to = $email;
            $this->addHeader('To', $this->to);
            return TRUE;
        }
    }

    function setSubject($subject = '', $allowempty = 'no')
    {
        $subject = trim($subject);
        if (!empty($subject))
        {
            $subject = str_replace("\n", "", $subject);
            $this->subject = $subject;
            return TRUE;
        }
        else
        {
            if ($allowempty == 'yes')
            {
                $this->subject = 'None';
                return TRUE;
            }
            else
            {
                return FALSE;
            }
        }
    }

    function setMessage($message = '')
    {
        $message = trim($message);
        if (!empty($message))
        {
            $this->message = wordwrap($message, 70, "\n");
            //$this->message = $message;
            return TRUE;
        }
        return FALSE;
    }

    // Optional calls
    function addBCC($email)
    {
        $email = trim($email);
        if (empty($email))
        {
            return FALSE;
        }
        else
        {
            $this->bcc[] = $email;
            return TRUE;
        }
    }

    function addCC($email)
    {
        $email = trim($email);
        if (empty($email))
        {
            return FALSE;
        }
        else
        {
            $this->cc[] = $email;
            return TRUE;
        }
    }

    function addHeader($name = '', $value = '')
    {
        if (!empty($name) && !empty($value))
        {
            $break = "\n";

            $this->headers .= $name.': '.$value.$break;
            return TRUE;
        }
        return FALSE;
    }

    // Committing calls
    function sendPHP()
    {
        if (!empty($this->cc))
        {
            $this->cc = trim(implode(', ', $this->cc), ', ');
            $this->addHeader('Cc', $this->cc);
        }

        if (!empty($this->bcc))
        {
            $this->bcc = trim(implode(', ', $this->bcc), ', ');
            $this->addHeader('Bcc', $this->bcc);
        }

        if (mail($this->to, $this->subject, $this->message, $this->headers))
        {
            return TRUE;
        }
        return FALSE;
    }

    function sendSMTP()
    {
        if (GAIABB_OS == 'WIN')
        {
            $this->message = str_replace("\n.", "\n..", $this->message);
        }

        if ($this->connectSMTP())
        {
            $this->dataSMTP();
            $this->disconnectSMTP();
            return TRUE;
        }
        return FALSE;
    }

    function connectSMTP()
    {
        global $CONFIG;

        if (empty($this->smtp_conn))
        {
            $this->smtp_conn = fsockopen($CONFIG['smtphost'], $CONFIG['smtpport'], $errno, $errstr, $CONFIG['smtptimeout']);
            if ($this->smtp_conn)
            {
                socket_set_blocking($this->smtp_conn, 0);
                return TRUE;
            }
            return FALSE;
        }
        return FALSE;
    }

    function disconnectSMTP()
    {
        if (!empty($this->smtp_conn))
        {
            fclose($this->smtp_conn);
            $this->smtp_conn = null;
            return TRUE;
        }
        return FALSE;
    }

    function SMTP_receive($cmd = '')
    {
        global $lang;

        if (!empty($this->smtp_conn))
        {
            $return = ''; $line = '';
            while (strpos($return, "\r\n") === false || substr($line,3,1) !== ' ')
            {
                $line = fgets($this->smtp_conn, 512);
                $return .= $line;
            }

            if (DEBUG && X_SADMIN)
            {
                echo "\n<!--\n$lang[Smtp_Csays]".$cmd."\n";
                if (!empty($return))
                {
                    echo "$lang[Smtp_Ssays]".$return."\n";
                }
                echo "--!>";
            }
        }
    }

    function SMTP_send($cmd = '', $lb = "\r\n")
    {
        global $lang;

        if (!empty($this->smtp_conn))
        {
            fputs($this->smtp_conn, $cmd.$lb);
        }
    }

    function dataSMTP()
    {
        global $CONFIG;

        if (!empty($this->smtp_conn))
        {
            $this->SMTP_receive('CONNECT');
            $this->SMTP_send('EHLO '.$CONFIG['smtpServer']);
            $this->SMTP_receive('EHLO');

            if (!empty($CONFIG['smtpusername']))
            {
                $this->SMTP_send('AUTH LOGIN');
                $this->SMTP_receive('AUTH LOGIN');
                $this->SMTP_send(base64_encode($CONFIG['smtpusername']));
                $this->SMTP_receive('USERNAME SENT');
                $this->SMTP_send(base64_encode($CONFIG['smtppassword']));
                $this->SMTP_receive('PASSWORD SENT');
            }

            $this->SMTP_send('MAIL FROM:'.$this->from);
            $this->SMTP_receive('MAIL FROM');
            $this->SMTP_send('RCPT TO:'.$this->to);

            $this->bcc = array_unique(array_merge($this->bcc, $this->cc));

            if (!empty($this->bcc))
            {
                foreach ($this->bcc as $rcpt_to)
                {
                    $this->SMTP_send('RCPT TO:'.$rcpt_to);
                }
            }

            $this->SMTP_receive('RCPT TO (RECIPIENT)');
            $this->SMTP_send('DATA');
            $this->SMTP_receive('DATA');
            $this->SMTP_send("To: $this->to\r\nFrom: $this->from\r\nSubject: $this->subject\r\n$this->headers\r\n$this->message", "\r\n.\r\n");
            $this->SMTP_receive('Data');
            $this->SMTP_send('QUIT');
            $this->SMTP_receive('QUIT');
            return TRUE;
        }
        return FALSE;
    }

    function Send()
    {
        global $charset, $CONFIG;

        $this->addHeader('Content-Type', 'text/plain; charset="'.$charset.'"');
        $this->addHeader('X-Mailer', 'GaiaBB Forum Software');
        $this->addHeader('X-AntiAbuse', 'Originating Site - '.$_SERVER['HTTP_HOST']);
        $this->addHeader('X-AntiAbuse', 'Request URI - '.$_SERVER['PHP_SELF']);
        $this->addHeader('X-AntiAbuse', 'Originating IP - '.$_SERVER['REMOTE_ADDR']);

        switch ($CONFIG['smtp_status'])
        {
            case 'on':
                $this->addHeader('X-AntiAbuse', 'Mail Method - SMTP');
                if ($this->sendSMTP())
                {
                    return TRUE;
                }
                break;
            default:
                $this->addHeader('X-AntiAbuse', 'Mail Method - MAIL');
                if ($this->sendPHP())
                {
                    return TRUE;
                }
        }
        return FALSE;
    }
}
?>