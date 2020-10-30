<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ReplyReviewAgain extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $ref;
    protected $comp;
    protected $date_request;
    public function __construct($ref,$comp,$date_request)
    {
        //
        $this->ref = $ref;
        $this->comp = $comp;
        $this->date_request = $date_request;
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
        return (new MailMessage)
        ->subject('For Approval Notification')
        ->greeting('Good day,')
        ->line('User successfully replied with the request you tag as "Review again".')
        ->line('Reference Number : '.$this->comp."-".$this->date_request."-".str_pad($this->ref, 4, '0', STR_PAD_LEFT))
        ->line('Please click the button provided for faster transaction')
        ->action('Pending For Approval', url('/'))
        ->line('Thank you for using our application!');
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
