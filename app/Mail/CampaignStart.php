<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CampaignStart extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $title;
    protected $object;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $subject = null, $object = null )
    {
        //
        $this->title= $subject;
        $this->object = $object;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject( $this->title );
        return $this->view('emails.campaign-start')->with( 'object', $this->object );
    }
}
