<?php
namespace App\Services\MailServices;

use App\Services\MailServices\IMailService;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailNotify;
use App\Jobs\SendEmail;

class MailService implements IMailService {
    public function notifyMail( $to, $mailObject = null,  $cc= null, $bcc=null) {
        $mail = Mail::to($to);

        if (isset($cc)) {
            $mail->cc($cc);
        }

        if (isset($bcc)) {
            $mail->bcc($bcc);
        }

        if (empty($mailObject)) {
            $mailObject = (new MailNotify())->onQueue('emails');
        }else {
            $mailObject = $mailObject->onQueue('emails');
        }

        return $mail->send($mailObject);
    }
}
?>