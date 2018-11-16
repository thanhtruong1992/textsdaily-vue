<?php
namespace App\Services\MailServices;

interface IMailService
{
    public function notifyMail( $to, $mailObject = null, $cc = null, $bcc = null );

}
?>