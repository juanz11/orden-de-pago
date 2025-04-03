<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .details {
            margin-bottom: 20px;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items th, .items td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .items th {
            background-color: #f8f9fa;
        }
        .total {
            font-weight: bold;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Nueva Orden de Pago Creada</h2>
        </div>

        <div class="details">
            <p><strong>Número de Orden:</strong> {{ $order->id }}</p>
            <p><strong>Solicitante:</strong> {{ $order->user->name }}</p>
            <p><strong>Departamento:</strong> {{ $order->user->department }}</p>
            <p><strong>Proveedor:</strong> {{ $order->supplier->name }}</p>
            <p><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y') }}</p>
        </div>

        <h3>Items de la Orden</h3>
        <table class="items">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Bs. {{ number_format($item->unit_price, 2) }}</td>
                    <td>Bs. {{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">
            <p>Total: Bs. {{ number_format($order->total, 2) }}</p>
        </div>

        <p>Para ver más detalles de la orden, ingrese al sistema.</p>
    </div>
</body>
</html>
