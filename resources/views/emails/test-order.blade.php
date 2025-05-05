<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nueva Orden</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff;">
        <h1 style="color: #333; text-align: center;">Nueva Orden #{{ $order->id }}</h1>
        
        <div style="margin: 20px 0; padding: 20px; background: #f8f9fa; border-radius: 5px;">
            <h2 style="color: #444; margin-bottom: 15px;">Detalles de la orden:</h2>
            <p style="margin: 10px 0;"><strong>Total:</strong> {{ number_format($order->total, 2, ',', '.') }} Bs.</p>
            <p style="margin: 10px 0;"><strong>Estado:</strong> {{ $order->status }}</p>
        </div>

        @if($token)
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('orders.approve-by-email', ['token' => $token]) }}"
               style="display: inline-block; padding: 12px 24px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
                Aprobar Orden
            </a>
            <p style="margin: 10px 0;">Token de aprobación: {{ $token }}</p>
        </div>
        @endif

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #666; font-size: 14px;">
            <p>Gracias,<br>{{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
