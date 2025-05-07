<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $token;

    public function __construct(Order $order, ?string $token = null)
    {
        $this->order = $order;
        $this->token = $token;
    }

    public function build()
    {
        return $this->subject("Nueva Orden de Pago #{$this->order->id}")
                    ->markdown('emails.new-order');
    }
}
