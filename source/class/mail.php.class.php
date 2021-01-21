<?php
/**
 * GaiaBB
 * Copyright (c) 2009-2021 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Forked from UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * Forked from XMB
 * Copyright (c) 2001 - 2021 The XMB Development Team
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

class PhpMail extends BaseMail
{
    public function __construct()
    {
    }

    public function setFrom($email, $name = '')
    {
        $retval = parent::setFrom($email, $name);

        if ($retval) {
            $this->addHeader('From', $this->from);
            $this->addHeader('Reply-To', $email);
        }

        return $retval;
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

    public function addHeader($name = '', $value = '')
    {
        return parent::addHeader($name, $value);
    }

    public function sendMail()
    {
        global $charset, $CONFIG;

        $this->addHeader('Content-Type', 'text/plain; charset="' . $charset . '"');
        $this->addHeader('X-Mailer', 'GaiaBB Forum Software');
        $this->addHeader('X-AntiAbuse', 'Originating Site - ' . $_SERVER['HTTP_HOST']);
        $this->addHeader('X-AntiAbuse', 'Request URI - ' . $_SERVER['PHP_SELF']);
        $this->addHeader('X-AntiAbuse', 'Originating IP - ' . $_SERVER['REMOTE_ADDR']);

        // Add the CC recipients
        if (!empty($this->cc)) {
            $this->cc = trim(implode(', ', $this->cc), ', ');
            $this->addHeader('Cc', $this->cc);
        }

        // Add the BCC recipients (primary way newsletters are sent)
        if (!empty($this->bcc)) {
            $this->bcc = trim(implode(', ', $this->bcc), ', ');
            $this->addHeader('Bcc', $this->bcc);
        }

        return mail($this->to, $this->subject, $this->message, $this->headers);
    }
}
