<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WalletChange extends Notification implements ShouldQueue
{
    use Queueable;

    private $template;
    private $status;
    private $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($template, $status, $user = null)
    {
        $this->template = $template;
        $this->status = $status;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $notifiable = $this->user ?: $notifiable;
        $from_name = email_setting('from_name', get_setting('site_name'));
        $from_email = email_setting('from_email', get_setting('site_email'));
        $template = EmailTemplate::get_template('wallet-change-'.$this->template);
        $message = $this->replace_shortcode($template->message, $notifiable);
        $greeting = $this->replace_shortcode($template->greeting, $notifiable);
        $regards = ($template->regards == 'true' ? get_setting('site_mail_footer', "Best Regards, \n[[site_name]]") : '');

        return (new MailMessage)
                    ->from($from_email, $from_name)
                    ->subject($this->replace_shortcode($template->subject, $notifiable))
                    ->markdown('mail.general', [
                        'user' => $notifiable,
                        'message' => $message,
                        'greeting' => $greeting,
                        'salutation' => $this->replace_shortcode($regards, $notifiable),
                    ]);
    }

    /**
     * Get the short-code and replace with data.
     *
     * @param  mixed  $code
     * @param  mixed  $notifiable
     * @return void
     */
    public function replace_shortcode($code, $notifiable)
    {
        $shortcode =array(
            "\n",
            '[[site_name]]',
            '[[site_email]]',
            '[[site_url]]',
            '[[support_email]]',
            '[[user_name]]',
            '[[user_email]]',
            '[[user_id]]',
            '[[status]]',
        );
        $replace = array(
            "<br>",
            site_info('name', false),
            site_info('email', false),
            url('/'),
            get_setting('site_support_email', site_info('email', false)),
            $notifiable->name,
            $notifiable->email,
            set_id($notifiable->id, 'user'),
            $this->status,
        );

        $return = str_replace($shortcode, $replace, $code);
        return $return;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
