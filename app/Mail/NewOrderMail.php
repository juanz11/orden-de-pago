<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $token;

    public function __construct(Order $order, $token = null)
    {
        $this->order = $order;
        $this->token = $token;
    }

    public function build()
    {
        return $this->subject('Nueva Orden de Pago #' . $this->order->id)
                    ->view('emails.new-order')
                    ->with([
                        'order' => $this->order,
                        'token' => $this->token
                    ]);
    }
}
