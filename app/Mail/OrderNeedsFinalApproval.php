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
        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('orders.pdf.order-details', [
                'order' => $this->order,
                'currency' => 'Bs.',
                'formatNumber' => function($number) {
                    return number_format($number, 2, ',', '.');
                },
                'formatExchangeRate' => function($number) {
                    return number_format($number, 2, ',', '.');
                },
                'exchangeRate' => null
            ]);

            return $this->subject("Orden de Pago #{$this->order->id} - Aprobación Final")
                        ->markdown('emails.order-needs-final-approval')
                        ->attachData($pdf->output(), "orden-de-pago-{$this->order->id}.pdf", [
                            'mime' => 'application/pdf'
                        ]);
        } catch (\Exception $e) {
            \Log::error('Error al generar PDF en OrderNeedsFinalApproval: ' . $e->getMessage());
            return $this->subject("Orden de Pago #{$this->order->id} - Aprobación Final")
                        ->markdown('emails.order-needs-final-approval');
        }
    }
}
