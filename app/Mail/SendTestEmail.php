<?php

namespace App\Mail;

use App\Models\EmailTemplate;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTestEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $user;
    private $slug;
    
    public function __construct($user, $slug)
    {
        $this->user = $user;
        $this->slug = $slug;
    }

    /**
     * Build the message.
     *
     * @return $this
     * @version 1.5.0
     * @return void
     */
    public function build()
    {
        $from_name = email_setting('from_name', get_setting('site_name'));
        $from_email = email_setting('from_email', get_setting('site_email'));

        $et = EmailTemplate::get_template($this->slug);

        $subject = $et->subject != '' ? replace_shortcode($et->subject) : 'Welcome to ' . $from_name;
        $et->greeting = replace_with($et->greeting, '[[user_name]]', "<strong>" . $this->user->name . "</strong>");
        $greeting = $et->greeting != '' ? replace_shortcode($et->greeting) : 'Hi ' . $this->user->name . ",";
        $et->regards = ($et->regards == 'true' ? get_setting('site_mail_footer') : null);
        $regards = $et->regards != '' ? replace_shortcode($et->regards) : null;
        $msg = $et->message != '' ? replace_shortcode($et->message) : 'Welcome to ' . $from_name;

        return $this->from($from_email, $from_name)
            ->subject($subject)
            ->markdown('mail.test', ['user' => $this->user, 'message' => $msg, 'template' => $et, 'greeting' => $greeting, 'salutation' => $regards]);
    }
}
