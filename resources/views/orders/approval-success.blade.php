<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>¡Aprobación Exitosa!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        .success-icon {
            color: #28a745;
            font-size: 48px;
            margin-bottom: 20px;
        }
        .message {
            color: #666;
            margin-bottom: 30px;
        }
        .status {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: bold;
            margin: 10px 0;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .order-info {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
            text-align: left;
        }
        .close-window {
            display: inline-block;
            padding: 10px 20px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            margin-top: 20px;
            cursor: pointer;
        }
        .close-window:hover {
            background-color: #5a6268;
        }
        .approvals-needed {
            margin-top: 20px;
            padding: 15px;
            background: #fff3cd;
            border-radius: 5px;
            color: #856404;
        }
        .approval-progress {
            margin-top: 15px;
            font-size: 1.1em;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✓</div>
        <h1>¡Aprobación Exitosa!</h1>
        
        <div class="order-info">
            <p><strong>Orden #{{ $order->id }}</strong></p>
            <p><strong>Solicitante:</strong> {{ $order->user->name }}</p>
            <p><strong>Departamento:</strong> {{ $order->user->department }}</p>
            <p><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y') }}</p>
            <p><strong>Estado actual:</strong> 
                <span class="status status-{{ $order->status === 'aprobado' ? 'approved' : 'pending' }}">
                    {{ ucfirst($order->status) }}
                </span>
            </p>
        </div>

        <div class="message">
            <p>¡Has aprobado exitosamente esta orden de pago!</p>
            
            @if($order->status === 'aprobado')
                <p>La orden ha sido completamente aprobada y será procesada para su pago.</p>
            @else
                <div class="approvals-needed">
                    <p>La orden aún necesita la aprobación de otros administradores para ser procesada.</p>
                    <div class="approval-progress">
                        {{ $order->approval_progress }}
                    </div>
                </div>
            @endif
        </div>

        <button class="close-window" onclick="window.close()">Cerrar Ventana</button>
    </div>
</body>
</html>
