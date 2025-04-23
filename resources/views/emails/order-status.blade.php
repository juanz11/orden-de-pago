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
        @if($isApproved && $order->admin)
        <p>Aprobada por: <strong>{{ $order->admin->name }}</strong></p>
        @endif

        <div class="details">
            <h3>Detalles de la Orden:</h3>
            <p><strong>Número de Orden:</strong> {{ $order->id }}</p>
            <p><strong>Proveedor:</strong> {{ $order->supplier ? $order->supplier->name : $order->other_supplier }}</p>
            @if($order->supplier && ($order->supplier->address || $order->supplier->contact_name))
            <p><strong>Detalles del Proveedor:</strong></p>
            @if($order->supplier->address)
            <p style="margin-left: 20px;"><strong>Dirección:</strong> {{ $order->supplier->address }}</p>
            @endif
            @if($order->supplier->contact_name)
            <p style="margin-left: 20px;"><strong>Contacto:</strong> {{ $order->supplier->contact_name }}</p>
            @endif
            @endif
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
