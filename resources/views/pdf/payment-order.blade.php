<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orden de Pago #{{ $order->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            margin-bottom: 10px;
        }
        .label {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            text-align: right;
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Orden de Pago</h1>
        <h2>Número de Orden: {{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</h2>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="label">Solicitante:</span> {{ $order->user->name }}
        </div>
        <div class="info-row">
            <span class="label">Departamento:</span> {{ $order->user->department }}
        </div>
        <div class="info-row">
            <span class="label">Proveedor:</span> {{ $order->supplier ? $order->supplier->name : $order->other_supplier }}
        </div>
        <div class="info-row">
            <span class="label">Fecha:</span> {{ $order->created_at->format('d/m/Y') }}
        </div>
    </div>

    <h3>Items de la Orden</h3>
    <table>
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
                <td>{!! $formatNumber($item->unit_price) !!}</td>
                <td>{!! $formatNumber($item->quantity * $item->unit_price) !!}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total: {!! $formatNumber($order->total) !!}
    </div>
</body>
</html>
