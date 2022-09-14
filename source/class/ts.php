<?php


require "sendgrid.class.php";

$i = new MailSys();

$i->setTo("vanderaj@greebo.net");
$i->setSubject("testing mail via sendgrid");
$i->setMessage("This should appear in the main body of the e-mail");

$i->Send();

?>
