<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orden de Compra</title>
    <style>
        @page {
            size: 214mm 277mm;
            margin: 15mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            max-width: 184mm; /* 214mm - 30mm for margins */
            margin: 0 auto;
        }
        .header {
            position: relative;
            margin-bottom: 20px;
        }
        .logo {
            position: absolute;
            top: 0;
            left: 0;
            width: 120px;
        }
        .company-info {
            margin-left: 130px;
            font-size: 10px;
        }
        .order-number {
            position: absolute;
            top: 0;
            right: 0;
            color: #000066;
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
        .supplier-info {
            margin-bottom: 20px;
        }
        .totals {
            width: 300px;
            margin-left: auto;
        }
        .observations {
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 10px;
        }
        .signatures {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature {
            width: 45%;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo/logo.png') }}" class="logo">
        <div class="company-info">
            <strong>SNC PHARMA, C.A.</strong><br>
            RIF: J-29855562-9<br>
            Centro Profesional Irabo, Piso 2, Oficina 2-34<br>
            Valencia, Edo. Carabobo - Venezuela. Código Postal 2001.<br>
            Telef: (+58) 0241-8243176/8249323.<br>
            Registrada MSDS No. C/R004.<br>
            Email: Soporte@sncpharma.com
        </div>
        <div class="order-number">
            <h2>Orden de Compra<br>Nro. - {{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</h2>
        </div>
    </div>

    <div class="supplier-info">
        <table>
            <tr>
                <td width="50%">
                    <strong>Proveedor:</strong> {{ $order->supplier ? $order->supplier->name : $order->other_supplier }}<br>
                    <strong>Dirección:</strong> {{ $order->supplier ? $order->supplier->address : '' }}<br>
                    <strong>R.I.F:</strong> {{ $order->supplier ? $order->supplier->rif : '' }}<br>
                    <strong>Teléfono:</strong> {{ $order->supplier ? $order->supplier->phone : '' }}
                </td>
                <td width="50%">
                    <strong>Fecha de la orden:</strong> {{ $order->created_at->format('d/m/Y') }}<br>
                    <strong>Fecha de Entrega:</strong> {{ $order->created_at->format('d/m/Y') }}<br>
                    <strong>Contacto:</strong> {{ $order->supplier ? $order->supplier->contact_name : '' }}<br>
                    <strong>Condición de Pago:</strong> {{ $order->supplier ? $order->supplier->payment_condition : '' }}
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th>Cantidad</th>
                <th>Precio Unit.</th>
                <th>Monto (Bs)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>00-{{ $loop->iteration }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                <td>{{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td><strong>SUB-TOTAL:</strong></td>
                <td>{{ number_format($order->total, 2, ',', '.') }}</td>
            </tr>
            {{-- <tr>
                <td><strong>I.V.A. (0%)</strong></td>
                <td>{{ number_format(0, 2, ',', '.') }}</td>
            </tr> --}}
            <tr>
                <td><strong>TOTAL</strong></td>
                <td>{{ number_format($order->total, 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    @if($order->observations)
    <div class="user-observations" style="margin: 20px 0;">
        <div style="display: flex; align-items: start;">
            <strong style="color: #ff0000;">OBSERVACIONES:</strong>
            <span style="margin-left: 5px; font-weight: bold; font-size: 0.9em; color: #000;">
                {!! nl2br(e($order->observations)) !!}
            </span>
        </div>
    </div>
    @endif

    <div class="company-instructions">
        <div style="border: 1px solid #ddd; padding: 10px; margin-top: 5px;">
            <strong>Es importante que se cumplan las indicaciones aquí señaladas.</strong><br>
            1.- Facturar a: SNC PHARMA, C.A. RIF: J-29855562-9, Dirección Fiscal: Centro Profesional<br>
            2.- Se cancela la Orden de Compra a la TASA del Banco Central de Venezuela -
        </div>
    </div>

    <div style="margin-top: 60px; font-size: 0.8em;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; padding: 20px; border: none;">
                    <div style="border-top: 1px solid black; text-align: center;">
                        <div style="margin-top: 5px;">Jefe de Compras</div>
                        <div>Joel A. Lopez J.</div>
                    </div>
                </td>
                <td style="width: 50%; padding: 20px; border: none;">
                    <div style="border-top: 1px solid black; text-align: center;">
                        <div style="margin-top: 5px;">Representante Legal</div>
                        <div>Julio H. Brandt T.</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
