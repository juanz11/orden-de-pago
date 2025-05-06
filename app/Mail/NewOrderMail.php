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
        // Obtener la tasa BCV actual
        $exchangeRate = $this->order->exchange_rate ?: 88.72;
        
        // Formatear números para Bs con punto como separador de miles
        $formatNumber = function($number) use ($exchangeRate) {
            return 'Bs. ' . number_format($number, 2, ',', '.');
        };

        // Formatear la tasa de cambio
        $formatExchangeRate = function() use ($exchangeRate) {
            return number_format($exchangeRate, 2, ',', '.');
        };

        $pdf = PDF::loadView('orders.pdf.order-details', [
            'order' => $this->order,
            'token' => $this->token,
            'currency' => 'bs',
            'formatNumber' => $formatNumber,
            'formatExchangeRate' => $formatExchangeRate,
            'exchangeRate' => $exchangeRate
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
