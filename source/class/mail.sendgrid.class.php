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

require_once ROOT . "lib/sendgrid-php/sendgrid-php.php";

class SendGridMail extends BaseMail
{
    private $email;         // Email to be sent

    public function __construct()
    {
        parent::__construct();

        $this->email = new \SendGrid\Mail\Mail();
    }

    public function setFrom($recipient, $name = '')
    {
        if (!parent::setFrom($recipient, $name)) {
            return false;
        }

        $this->email->setFrom($recipient, $this->name);
        $this->email->setReplyTo($recipient);
        return true;
    }

    public function setTo($recipient)
    {
        if (!parent::setTo($recipient)) {
            return false;
        }

        $this->email->addTo($this->to);
        return true;
    }

    public function setSubject($subject, $allowempty = 'no')
    {
        if (!parent::setSubject($subject, $allowempty)) {
            return false;
        }

        $subject = str_replace("\n", "", $this->subject);

        $this->email->setSubject($subject);
        return true;
    }

    public function setMessage($message)
    {
        if (!parent::setMessage($message)) {
            return false;
        }

        $this->email->addContent("text/plain", $this->message);
        return true;
    }

    public function addBCC($recipient)
    {
        $recipient = trim($recipient);
        if (empty($recipient)) {
            return false;
        }

        $sgRecipient = [
            $recipient, ""
        ];

        $this->bcc[] = $sgRecipient;
        return true;
    }

    public function addCC($recipient)
    {
        $recipient = trim($recipient);
        if (empty($recipient)) {
            return false;
        }

        $sgRecipient = [
            $recipient, ""
        ];

        $this->cc[] = $sgRecipient;
        return true;
    }

    public function sendMail()
    {
        global $sendgridAPIkey;

        $sendgrid = new \SendGrid($sendgridAPIkey);
        try {
            if (!empty($this->cc)) {
                $this->email->addCcs($this->cc);
            }
            
            if (!empty($this->bcc)) {
                $this->email->addBccs($this->bcc);
            }

            $response = $sendgrid->send($this->email);
        } catch (\Exception $e) {
            echo 'Caught exception: ' . $e->getMessage() . "\n";
        }
    }
}
