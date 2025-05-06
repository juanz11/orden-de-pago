<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Estado de la Orden</title>
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
        .message {
            color: #666;
            margin-bottom: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .info-message {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
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
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Estado de la Orden {{ $order ? "#".$order->id : "" }}</h1>
        
        @if(isset($error) && $error)
            <div class="error-message">
                {{ $error }}
            </div>
        @endif

        @if(isset($message) && $message)
            <div class="info-message">
                {{ $message }}
            </div>
        @endif

        @if($order)
            <div class="order-info">
                <p><strong>Solicitante:</strong> {{ $order->user->name }}</p>
                <p><strong>Departamento:</strong> {{ $order->user->department }}</p>
                <p><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y') }}</p>
                <p><strong>Estado actual:</strong> 
                    <span class="status status-{{ $order->status === 'aprobado' ? 'approved' : ($order->status === 'pendiente' ? 'pending' : 'rejected') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </p>
            </div>

            <div class="message">
                {{ $order->approval_progress }}
            </div>
        @endif

        <button class="close-window" onclick="window.close()">Cerrar Ventana</button>
    </div>
</body>
</html>
