<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ServerDetails extends Mailable
{
    use Queueable, SerializesModels;

    public $launcher;

    /**
     * ServerDetails constructor.
     * @param $launcher
     */
    public function __construct( $launcher )
    {
        $this->launcher = $launcher;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('Server Details - Launch a WP Server')
            ->markdown('emails.serverdetails');
    }
}
