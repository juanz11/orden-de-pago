<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orden de Pago</title>
    <style>
        @page {
            size: 214mm 277mm;
            margin: 15mm;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0 0 5px 0;
        }
        .info {
            margin-bottom: 15px;
            line-height: 1.3;
        }
        .info p {
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 11px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
        }
        th {
            background-color: #f0f0f0;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals {
            margin-top: 10px;
            text-align: right;
        }
        .totals p {
            margin: 3px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Orden de Pago #{{ $order->id }}</h1>
        <p><strong>Fecha:</strong> {{ $order->created_at->format('d/m/Y') }}</p>
    </div>

    <div class="info">
        <div style="margin-bottom: 15px;">
            <p><strong>Solicitante:</strong> {{ $order->user->name }}</p>
            <p><strong>Departamento:</strong> {{ $order->user->department }}</p>
            <p><strong>Proveedor:</strong> {{ $order->supplier ? $order->supplier->name : $order->other_supplier }}</p>
        </div>
        <p><strong>Es importante que se cumplan las indicaciones aquí señaladas:</strong></p>
        <p>1.- SNC PHARMA, C.A. RIF: J-29855562-9, Dirección Fiscal: Centro Profesional</p>
        <p>2.- Se cancela la Orden de Compra a la TASA del Banco Central de Venezuela</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th class="text-center" width="10%">Cantidad</th>
                <th class="text-right" width="20%">Precio Unitario (BS)</th>
                <th class="text-right" width="20%">Total (BS)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">{{ $formatNumber($item->unit_price) }} </td>
                <td class="text-right">{{ $formatNumber($item->quantity * $item->unit_price) }} </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <p><strong>SUB-TOTAL:</strong> {{ $formatNumber($order->total) }} Bs</p>
        <p><strong>TOTAL:</strong> {{ $formatNumber($order->total) }} Bs</p>
    </div>
</body>
</html>

    <div style="margin-top: 20px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 60%; vertical-align: top; padding-right: 20px;">
                    @if($order->observations)
                    <strong style="color: #ff0000;">OBSERVACIONES:</strong>
                    <span style="margin-left: 5px; font-weight: bold; font-size: 0.9em;">{{ $order->observations }}</span>
                    @endif
                </td>
               
            </tr>
        </table>
    </div>


  
</body>
</html>
