<?php
/**
 * GaiaBB
 * Copyright (c) 2009-2020 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Forked from UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * Forked from XMB
 * Copyright (c) 2001 - 2020 The XMB Development Team
 * https://forums.xmbforum2.com/
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
// phpcs:disable PSR1.Files.SideEffects

namespace GaiaBB;

class SmtpMail extends BaseMail
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setFrom($email, $name = '')
    {
        parent::setFrom($email, $name);

        $this->addHeader('From', $this->from);
        $this->addHeader('Reply-To', $email);
        return true;
    }

    public function setTo($email)
    {
        return parent::setTo($email);
    }

    public function setSubject($subject, $allowempty = 'no')
    {
        return parent::setSubject($subject, $allowempty);
    }

    public function setMessage($message)
    {
        return parent::setMessage($message);
    }

    public function addBCC($email)
    {
        return parent::addBCC($email);
    }

    public function addCC($email)
    {
        return parent::addCC($email);
    }

    public function addHeader($name, $value = '')
    {
        return parent::addHeader($name, $value);
    }

    public function connectSMTP()
    {
        global $CONFIG;

        if (empty($this->smtp_conn)) {
            $this->smtp_conn = fsockopen(
                $CONFIG['smtphost'],
                $CONFIG['smtpport'],
                $errno,
                $errstr,
                $CONFIG['smtptimeout']
            );
            if ($this->smtp_conn) {
                socket_set_blocking($this->smtp_conn, 0);
                return true;
            }
            return false;
        }
        return false;
    }

    public function disconnectSMTP()
    {
        if (!empty($this->smtp_conn)) {
            fclose($this->smtp_conn);
            $this->smtp_conn = null;
            return true;
        }
        return false;
    }

    public function smtpReceive($cmd)
    {
        global $lang;

        if (!empty($this->smtp_conn)) {
            $return = '';
            $line = '';
            while (strpos($return, "\r\n") === false || substr($line, 3, 1) !== ' ') {
                $line = fgets($this->smtp_conn, 512);
                $return .= $line;
            }

            if (DEBUG && X_SADMIN) {
                echo "\n<!--\n$lang[Smtp_Csays]" . $cmd . "\n";
                if (!empty($return)) {
                    echo "$lang[Smtp_Ssays]" . $return . "\n";
                }
                echo "--!>";
            }
        }
    }

    public function smtpSend($cmd, $lb = "\r\n")
    {
        global $lang;

        if (!empty($this->smtp_conn)) {
            fputs($this->smtp_conn, $cmd . $lb);
        }
    }

    public function dataSMTP()
    {
        global $CONFIG;

        if (!empty($this->smtp_conn)) {
            $this->smtpReceive('CONNECT');
            $this->smtpSend('EHLO ' . $CONFIG['smtpServer']);
            $this->smtpReceive('EHLO');

            if (!empty($CONFIG['smtpusername'])) {
                $this->smtpSend('AUTH LOGIN');
                $this->smtpReceive('AUTH LOGIN');
                $this->smtpSend(base64_encode($CONFIG['smtpusername']));
                $this->smtpReceive('USERNAME SENT');
                $this->smtpSend(base64_encode($CONFIG['smtppassword']));
                $this->smtpReceive('PASSWORD SENT');
            }

            $this->smtpSend('MAIL FROM:' . $this->from);
            $this->smtpReceive('MAIL FROM');
            $this->smtpSend('RCPT TO:' . $this->to);

            $this->bcc = array_unique(array_merge($this->bcc, $this->cc));

            if (!empty($this->bcc)) {
                foreach ($this->bcc as $rcpt_to) {
                    $this->smtpSend('RCPT TO:' . $rcpt_to);
                }
            }

            $this->smtpReceive('RCPT TO (RECIPIENT)');
            $this->smtpSend('DATA');
            $this->smtpReceive('DATA');
            $this->smtpSend(
                "To: $this->to\r\nFrom: $this->from\r\nSubject: $this->subject\r\n$this->headers\r\n$this->message",
                "\r\n.\r\n"
            );
            $this->smtpReceive('Data');
            $this->smtpSend('QUIT');
            $this->smtpReceive('QUIT');
            return true;
        }
        return false;
    }

    public function sendMail()
    {
        global $charset, $CONFIG;

        $this->addHeader('Content-Type', 'text/plain; charset="' . $charset . '"');
        $this->addHeader('X-Mailer', 'GaiaBB Forum Software');
        $this->addHeader('X-AntiAbuse', 'Originating Site - ' . $_SERVER['HTTP_HOST']);
        $this->addHeader('X-AntiAbuse', 'Request URI - ' . $_SERVER['PHP_SELF']);
        $this->addHeader('X-AntiAbuse', 'Originating IP - ' . $_SERVER['REMOTE_ADDR']);
        $this->addHeader('X-AntiAbuse', 'Mail Method - SMTP');

        if (substr(PHP_OS, 0, 3) == 'WIN') {
            $this->message = str_replace("\n.", "\n..", $this->message);
        }

        if ($this->connectSMTP()) {
            $this->dataSMTP();
            $this->disconnectSMTP();
            return true;
        }

        return false;
    }
}
