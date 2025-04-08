<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .status { font-weight: bold; color: #007bff; }
        .details { margin: 20px 0; }
        .footer { margin-top: 30px; font-size: 0.9em; color: #6c757d; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Actualización de Orden de Pago</h2>
        </div>

        <p>Hola {{ $order->user->name }},</p>

        <p>Tu orden de pago ha sido <span class="status">{{ $isApproved ? 'aprobada' : 'rechazada' }}</span>.</p>

        <div class="details">
            <h3>Detalles de la Orden:</h3>
            <p><strong>Número de Orden:</strong> {{ $order->id }}</p>
            <p><strong>Proveedor:</strong> {{ $order->supplier ? $order->supplier->name : $order->other_supplier }}</p>
            <p><strong>Total:</strong> ${{ number_format($order->total, 2) }}</p>
            @if($order->admin_comments)
            <p><strong>Comentarios:</strong> {{ $order->admin_comments }}</p>
            @endif
        </div>

        <div class="footer">
            <p>Este es un mensaje automático, por favor no responda a este correo.</p>
        </div>
    </div>
</body>
</html>
