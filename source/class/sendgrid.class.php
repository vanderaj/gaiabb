<?php
/**
 * GaiaBB
 * Copyright (c) 2011-2022 The GaiaBB Group
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

if (!defined('IN_PROGRAM') && (defined('DEBUG') && DEBUG == false)) {
    exit('This file is not designed to be called directly');
}

require __DIR__ . "/sendgrid-php/sendgrid-php.php";

class MailSys
{
    private $sendgrid;
    private $email;

    // Instantiate the class
    public function MailSys()
    {
        $this->email = new \SendGrid\Mail\Mail();

        $this->email->setReplyTo("admin@aussieveedubbers.com");
        $this->email->setFrom("admin@aussieveedubbers.com", "Aussieveedubbers");
    }

    // Required calls
    public function setFrom($email, $name = '')
    {
        $this->email->setFrom($email, $name);
        $this->email->setReplyTo($email);
        return true;
    }

    public function setTo($email)
    {
        $email = trim($email);
        if (empty($email)) {
            return false;
        } else {
            $this->email->addTo($email);
            return true;
        }
    }

    public function setSubject($subject = '', $allowempty = 'no')
    {
        $subject = trim($subject);
        if (!empty($subject)) {
            $subject = str_replace("\n", "", $subject);
            $this->email->setSubject($subject);
            return true;
        } else {
            if ($allowempty == 'yes') {
                $this->email->setSubject("none");
                return true;
            } else {
                return false;
            }
        }
    }

    public function setMessage($message = '')
    {
        $message = trim($message);
        if (!empty($message)) {
            $this->email->addContent("text/plain", wordwrap($message, 70, "\n"));
            return true;
        }
        return false;
    }

    // Optional calls
    public function addBCC($email)
    {
        $email = trim($email);
        if (!empty($email)) {
            $this->email->addTo($email);
            return true;
        }
        return false;
    }

    public function addCC($email)
    {
        $email = trim($email);
        if (!empty($email)) {
            $this->email->addTo($email);
            return true;
        }
        return false;
    }

    // Committing calls

    public function Send()
    {
        global $sendgridAPIkey;

        $sendgrid = new \SendGrid($sendgridAPIkey);
        try {
            $response = $sendgrid->send($this->email);
        } catch (Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }
}
