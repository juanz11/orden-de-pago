<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comprobante de Pago</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 2cm;
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
            background-color: #f8f9fa;
        }
        .total {
            text-align: right;
            margin-top: 20px;
            font-weight: bold;
        }
        .payment-info {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Comprobante de Pago</h1>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="label">Número de Orden:</span>
            {{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
        </div>
        <div class="info-row">
            <span class="label">Solicitante:</span>
            {{ $order->user->name }}
        </div>
        <div class="info-row">
            <span class="label">Departamento:</span>
            {{ $order->user->department }}
        </div>
        <div class="info-row">
            <span class="label">Proveedor:</span>
            {{ $order->supplier ? $order->supplier->name : $order->other_supplier }}
        </div>
        <div class="info-row">
            <span class="label">Fecha de Pago:</span>
            {{ $payment->created_at->format('d/m/Y') }}
        </div>
    </div>

    <div class="payment-info">
        <h3>Información del Pago</h3>
        <div class="info-row">
            <span class="label">Número de Referencia:</span>
            {{ $payment->reference_number }}
        </div>
        <div class="info-row">
            <span class="label">Porcentaje Pagado:</span>
            {{ number_format($payment->percentage, 1) }}%
        </div>
        <div class="info-row">
            <span class="label">Monto Pagado:</span>
            <x-format-currency :amount="$payment->amount" />
        </div>
        <div class="info-row">
            <span class="label">Registrado por:</span>
            {{ $payment->user->name }}
        </div>
    </div>

    <div class="info-section">
        <h3>Detalles de la Orden</h3>
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
                    <td><x-format-currency :amount="$item->unit_price" /></td>
                    <td><x-format-currency :amount="$item->quantity * $item->unit_price" /></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="total">
        <div class="info-row">
            <span class="label">Total de la Orden:</span>
            <x-format-currency :amount="$order->total" />
        </div>
    </div>
</body>
</html>
