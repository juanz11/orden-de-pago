<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Órdenes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .summary {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f3f4f6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 20px;
        }
        .status-pendiente {
            color: #92400e;
        }
        .status-aprobado {
            color: #065f46;
        }
        .status-rechazado {
            color: #991b1b;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Órdenes</h1>
        <p>Período: {{ $start_date }} - {{ $end_date }}</p>
        <p>Departamento: {{ $department }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Proveedor</th>
                <th>Items</th>
                <th>Total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                <td>{{ $order->user->name }}</td>
                <td>{{ $order->supplier ? $order->supplier->name : $order->other_supplier }}</td>
                <td>
                    @foreach($order->items as $item)
                        {{ $item->description }} ({{ $item->quantity }} x {{ number_format($item->unit_price, 2, ',', '.') }} Bs.)<br>
                    @endforeach
                </td>
                <td>{{ number_format($order->total, 2, ',', '.') }} Bs.</td>
                <td class="status-{{ $order->status }}">{{ ucfirst($order->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total General: {{ number_format($total, 2, ',', '.') }} Bs.
    </div>
</body>
</html>
