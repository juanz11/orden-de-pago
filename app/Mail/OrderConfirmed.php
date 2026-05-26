<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $confirmedBy;
    public $pendingAdmins;
    public $observation;

    public function __construct(Order $order, User $confirmedBy, array $pendingAdmins, ?string $observation = null)
    {
        $this->order = $order;
        $this->confirmedBy = $confirmedBy;
        $this->pendingAdmins = $pendingAdmins;
        $this->observation = $observation;
    }

    public function build()
    {
        return $this->subject("Orden de Pago #{$this->order->id} confirmada")
                    ->markdown('emails.order-confirmed');
    }
}
