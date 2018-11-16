<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExportCenter extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $title;
    protected $object;
    protected $attachFile;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct( $subject = null, $object = null , $attachFile = [])
    {
        //
        $this->title= $subject;
        $this->object = $object;
        $this->attachFile = $attachFile;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject( $this->title );
        $query = $this->view('emails.export-center')->with( 'object', $this->object );
        if(count($this->attachFile) > 0) {
            foreach($this->attachFile as $file) {
                $query->attach($file);
            }
        }
        return $query;
    }
}
