<?php

namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Contact_usMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $message;



    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Contact Form Message';

        $setting = Setting::first();

        return $this->subject($subject)->from('info@superpower.ae','superpower')->
        markdown('emails.contact_us_email',[
            'details' => $this->message,
        ]);

    }

    
}
