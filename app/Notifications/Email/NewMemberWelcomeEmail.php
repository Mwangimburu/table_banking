<?php

namespace App\Notifications\Email;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewMemberWelcomeEmail extends Notification implements ShouldQueue
{
    use Queueable;

    private $member;


    /**
     * NewMemberWelcomeSms constructor.
     * @param $member
     */
    public function __construct($member)
    {
        $this->member = $member;
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
        $template = EmailTemplate::where('name', 'new_member_welcome')->get()->first();
        $subject = $template['subject'];
        $body = $template['body'];

        $subject = str_replace('{first_name}', $notifiable['first_name'], $subject);
        $subject = str_replace('{middle_name}', $notifiable['middle_name'], $subject);
        $subject = str_replace('{last_name}', $notifiable['last_name'], $subject);
        $subject = str_replace('{phone}',  $notifiable['phone'], $subject);

        $body = str_replace('{first_name}', $notifiable['first_name'], $body);
        $body = str_replace('{middle_name}', $notifiable['middle_name'], $body);
        $body = str_replace('{last_name}', $notifiable['last_name'], $body);
        $body = str_replace('{phone}',  $notifiable['phone'], $body);

        return (new MailMessage)
            ->subject($subject)
           // ->greeting('') // example: Dear Sir, Hello Madam, etc ...
            ->line($body);
          //  ->salutation('Thank you.');  // example: best regards, thanks, etc ...
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
