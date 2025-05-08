<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderNeedsFinalApproval extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $token;

    public function __construct(Order $order, string $token)
    {
        $this->order = $order;
        $this->token = $token;
    }

    public function build()
    {
        return $this->subject("Orden de Pago #{$this->order->id} - Aprobación Final")
                    ->markdown('emails.order-needs-final-approval');
    }
}
