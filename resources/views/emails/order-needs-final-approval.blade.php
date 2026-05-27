<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Aprobación Final de Orden de Pago</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff;">
        <h1 style="color: #333; text-align: center;">Aprobación Final - Orden de Pago #{{ $order->id }}</h1>
        
        <div style="margin: 20px 0; padding: 20px; background: #f8f9fa; border-radius: 5px;">
            <h2 style="color: #444; margin-bottom: 15px;">Detalles de la orden:</h2>
            <p style="margin: 10px 0;"><strong>Solicitante:</strong> {{ $order->user->name }}</p>
            <p style="margin: 10px 0;"><strong>Departamento:</strong> {{ $order->user->department }}</p>
            <p style="margin: 10px 0;"><strong>Proveedor:</strong> {{ $order->supplier ? $order->supplier->name : $order->other_supplier }}</p>
            <p style="margin: 10px 0;"><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y') }}</p>
        </div>

        <div style="margin: 20px 0;">
            <h3 style="color: #444;">Items de la Orden</h3>
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="background: #f1f1f1;">
                        <th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Descripción</th>
                        <th style="padding: 10px; text-align: center; border: 1px solid #ddd;">Cantidad</th>
                        <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">Precio Unitario</th>
                        <th style="padding: 10px; text-align: right; border: 1px solid #ddd;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $item->description }}</td>
                        <td style="padding: 10px; text-align: center; border: 1px solid #ddd;">{{ $item->quantity }}</td>
                        <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">{{ number_format($item->unit_price, 2, ',', '.') }} Bs.</td>
                        <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">{{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }} Bs.</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="padding: 10px; text-align: right; border: 1px solid #ddd;"><strong>Total:</strong></td>
                        <td style="padding: 10px; text-align: right; border: 1px solid #ddd;"><strong>{{ number_format($order->total, 2, ',', '.') }} Bs.</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div style="margin: 20px 0; padding: 20px; background: #f8f9fa; border-radius: 5px;">
            <h3 style="color: #444; margin-bottom: 15px;">Aprobaciones Actuales:</h3>
            @foreach($approvedApprovals as $approval)
                <p style="margin: 10px 0;">✓ {{ $approval->user->name ?? 'Usuario no disponible' }} - {{ $approval->user->role ?? 'Rol no disponible' }}</p>
            @endforeach
        </div>

        @if($approvedApprovals->contains(fn ($approval) => filled($approval->comments)))
            <div style="margin: 25px 0; padding: 24px; background: #fff3cd; border: 2px solid #ffc107; border-radius: 8px;">
                <h2 style="color: #856404; margin: 0 0 18px 0; font-size: 24px; text-align: center;">Observaciones de las aprobaciones</h2>
                @foreach($approvedApprovals as $approval)
                    @if($approval->comments)
                        <div style="margin: 14px 0; padding: 18px; background: #ffffff; border-left: 6px solid #ffc107; border-radius: 5px;">
                            <p style="margin: 0 0 8px 0; color: #856404; font-size: 18px; font-weight: bold;">Observación de {{ $approval->user->name ?? 'Usuario no disponible' }}</p>
                            <p style="margin: 0; color: #333; font-size: 20px; font-weight: bold;">{{ $approval->comments }}</p>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            <p style="margin: 10px 0;">Esta orden requiere su aprobación final. Por favor, haga clic en el botón a continuación para aprobarla:</p>
            <!-- Botón compatible con Outlook -->
            <table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:separate;line-height:100%;">
              <tr>
                <td align="center" bgcolor="#4CAF50" role="presentation" style="border:none;border-radius:3px;cursor:auto;mso-padding-alt:10px 25px;background:#4CAF50;" valign="middle">
                  <a href="{{ route('orders.approve-by-email', ['token' => $token]) }}"
                     style="display:inline-block;background:#4CAF50;color:#ffffff;font-family:Arial, sans-serif;font-size:16px;font-weight:bold;line-height:120%;margin:0;text-decoration:none;text-transform:none;padding:10px 25px;mso-padding-alt:0px;border-radius:3px;"
                     target="_blank">
                    Aprobar Orden
                  </a>
                </td>
              </tr>
            </table>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; color: #666; font-size: 14px;">
            <p>Gracias,<br>{{ config('app.name') }}</p>
            <p style="font-size: 12px; color: #999;">Si el botón no funciona, puede copiar y pegar este enlace en su navegador:<br>
            {{ route('orders.approve-by-email', ['token' => $token]) }}</p>
        </div>
    </div>
</body>
</html>
