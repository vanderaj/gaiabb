<?php
/**
 * GaiaBB
 * Copyright (c) 2009-2020 The GaiaBB Project
 * https://github.com/vanderaj/gaiabb
 *
 * Based off UltimaBB
 * Copyright (c) 2004 - 2007 The UltimaBB Group
 * (defunct)
 *
 * Based off XMB
 * Copyright (c) 2001 - 2004 The XMB Development Team
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

abstract class BaseMail
{
    private $from;
    private $name;
    private $to;
    private $cc;
    private $bcc;
    private $subject;
    private $message;
    private $headers;

    // Instantiate the class
    public function __construct()
    {
    }

    /**
     * setFrom() - set the from mail header
     *
     * Set the "From" mail header, with an optional name
     *
     * @param string $from The email address of the sender
     * @param string $name Optional: the sender's name
     * @return boolean true if set, false if empty $from
     **/
    public function setFrom($from, $name = '')
    {
        $from = trim($from);
        if (empty($from)) {
            return false;
        }

        $name = trim($name);
        if (!empty($name)) {
            $this->from = $name . ' <' . $from . '>';
            $this->name = $name;
        } else {
            $this->from = $from;
        }

        return true;
    }

    /**
     * setTo - sets the recipient in the To: header
     *
     * Sets the recipient in the To: header
     *
     * @param string $recipient the recipient of the email
     * @return boolean true if set, false if recipient is blank or empty
     **/
    public function setTo($recipient)
    {
        $recipient = trim($recipient);
        if (empty($recipient)) {
            return false;
        }
        $this->to = $recipient;
        return true;
    }

    /**
     * setSubject - set's subject for the email
     *
     * Sets the subject for the email, should always be something interesting to prevent spam blocking
     *
     * @param string $subject       the subject of the email
     * @param (yes,no) $allowempty  can the subject be empty?
     * @return boolean              true if set, false if not set
     **/
    public function setSubject($subject, $allowempty = 'no')
    {
        $subject = trim($subject);

        if (empty($subject) && $allowempty == 'yes') {
            $this->subject = 'None';
            return true;
        } else {
            return false;
        }

        $subject = str_replace("\n", "", $subject);
        $this->subject = $subject;
        return true;
    }

    /**
     * setMessage - sets the email body
     *
     * Set the email body
     *
     * @param string $message       The email body, should be plain text
     * @return boolean              true if set, false if message was empty
     **/
    public function setMessage($message)
    {
        $message = trim($message);
        if (empty($message)) {
            return false;
        }

        $this->message = wordwrap($message, 70, "\n");
        return true;
    }

    /**
     * addBCC() - Add a blind carbon copy recipient
     *
     * Used by admin mail out functions. Adds the email address to the
     * internal array, which is used to send newsletters, etc.
     *
     * @param string $recipient     blind carbon copy recipient to be added
     * @return boolean          true if set, false if empty or not set
     **/
    public function addBCC($recipient)
    {
        $recipient = trim($recipient);
        if (empty($recipient)) {
            return false;
        }

        $this->bcc[] = $recipient;
        return true;
    }

   /**
     * addCC() - Add a carbon copy recipient
     *
     * Adds the email address to the internal array, which is used to send
     * emails to multiple recipients
     *
     * @param string $recipient     carbon copy recipient to be added
     * @return boolean          true if set, false if empty or not set
     **/
    public function addCC($recipient)
    {
        $recipient = trim($recipient);
        if (empty($recipient)) {
            return false;
        } else {
            $this->cc[] = $recipient;
            return true;
        }
    }

    /**
     * addHeader() - adds a SMTP header
     *
     * For various anti-spam reasons, we need to make our mails a bit
     * more "normal" to be accepted by many mail systems. This adds a
     * header to an internal array, which is then sent as part of the mail
     *
     * @param string $name      header name
     * @param string $value     header value
     * @return boolean          true if header added, false if empty
     **/
    public function addHeader($name, $value)
    {
        if (empty($name) || empty($value)) {
            return false;
        }

        $this->headers .= $name . ': ' . $value . "\n";
        return true;
    }

    /**
     * sendMail() - actually sends the mail
     *
     * Each mail delivery agent has its own way of sending mail. This
     * abstract function sends the mail using the MDA's API or raw
     * socket connectivity.
     *
     * @return boolean          true if header added, false if empty
     **/
    abstract public function sendMail();
}
