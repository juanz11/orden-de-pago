<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        Log::info('Estado actual de la orden: ' . $this->order->status);
        $isApproved = $this->order->status === Order::STATUS_APPROVED;
        Log::info('¿Es aprobada?: ' . ($isApproved ? 'Sí' : 'No'));
        
        $subject = 'Orden de Pago ' . ($isApproved ? 'Aprobada' : 'Rechazada');
        
        return $this->subject($subject)
                    ->view('emails.order-status')
                    ->with([
                        'order' => $this->order,
                        'isApproved' => $isApproved
                    ]);
    }
}
