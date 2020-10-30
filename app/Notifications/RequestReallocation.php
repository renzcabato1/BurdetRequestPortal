<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RequestReallocation extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    // protected $total;
    protected $ref;
    protected $comp;
    public function __construct($ref,$comp)
    {
        //
        // $this->total = $total;
        $this->ref = $ref;
        $this->comp = $comp;
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
        ->subject('Request Notification Reallocation')
        ->greeting('Good day,')
        ->line('Your Request Successfully Submitted!.')
        ->line('Reference Number : '.$this->comp."-".date('Ym')."-".str_pad($this->ref, 4, '0', STR_PAD_LEFT))
        ->action('Notification Action', url('/sb-request'))
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
