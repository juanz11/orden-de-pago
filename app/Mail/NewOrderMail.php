<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $pdf = PDF::loadView('orders.pdf.order-details', [
            'order' => $this->order,
            'token' => $this->token
        ]);
        
        // Configurar el tamaño de página a 214 × 277 mm
        $pdf->setPaper([0, 0, 606.77, 785.2]); // Convertido de mm a puntos
        
        return $this->view('emails.new-order')
            ->subject('Nueva Orden de Pago #' . $this->order->id)
            ->with([
                'order' => $this->order,
                'token' => $this->token
            ])
            ->attachData(
                $pdf->output(),
                'orden-de-pago-' . $this->order->id . '.pdf',
                [
                    'mime' => 'application/pdf'
                ]
            );
    }
}
