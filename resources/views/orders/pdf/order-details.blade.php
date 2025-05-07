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
            font-size: 0.75em;
            line-height: 1.4;
            max-width: 184mm;
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
            padding: 1px;
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
        .company-instructions {
            margin-top: 20px;
        }
        .company-instructions strong {
            display: block;
            margin-bottom: 10px;
        }
        .company-instructions p {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logo/logo.png') }}" class="logo">
        <div class="company-info">
            <strong>SNC PHARMA, C.A.</strong><br>
            RIF: J-29855562-9<br>
            Centro Profesional Prebo, Piso 2, Oficina 2-14<br>
            Valencia, Edo. Carabobo - Venezuela. Código Postal 2001.<br>
            Telef: (+58) 0241-8243176/8249323.<br>
            Registrada MSDS No. C/R 502.<br>
            Email: Soporte@sncpharma.com
        </div>
        <div class="order-number">
            <h2>Orden de Compra Nro. {{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</h2>
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
                    <strong>Condición de Pago:</strong> {{ $order->payment_condition ?: 'N/A' }}<br>
                    <strong>Tasa BCV:</strong> {{ $formatExchangeRate() }} Bs/USD
                </td>
            </tr>
        </table>
    </div>

    <table style="width: 100%; border-collapse: collapse; font-size: 0.75em; margin-top: 8px;">
        <thead>
            <tr>
                <th style="width: 7%; padding: 2px; text-align: left; border-bottom: 1px solid #000; font-size: 0.8em;">Item</th>
                <th style="width: 43%; padding: 2px; text-align: left; border-bottom: 1px solid #000; font-size: 0.8em;">Descripción</th>
                <th style="width: 8%; padding: 2px; text-align: center; border-bottom: 1px solid #000; font-size: 0.8em;">Cant</th>
                <th style="width: 21%; padding: 2px; text-align: right; border-bottom: 1px solid #000; font-size: 0.8em;">Precio Unit. {{ $currency === 'usd' ? '($)' : '(Bs)' }}</th>
                <th style="width: 21%; padding: 2px; text-align: right; border-bottom: 1px solid #000; font-size: 0.8em;">Monto {{ $currency === 'usd' ? '($)' : '(Bs)' }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td style="padding: 1px 2px; text-align: left;">{{ $loop->iteration }}</td>
                <td style="padding: 1px 2px; text-align: left;">{{ $item->description }}</td>
                <td style="padding: 1px 2px; text-align: center;">{{ $item->quantity }}</td>
                <td style="padding: 1px 2px; text-align: right;">{{ $formatNumber($item->unit_price) }}</td>
                <td style="padding: 1px 2px; text-align: right;">{{ $formatNumber($item->quantity * $item->unit_price) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 60%; vertical-align: top; padding-right: 20px;">
                    @if($order->observations)
                    <strong style="color: #ff0000;">OBSERVACIONES:</strong>
                    <span style="margin-left: 5px; font-weight: bold; font-size: 0.9em;">{{ $order->observations }}</span>
                    @endif
                </td>
                <td style="width: 40%; vertical-align: top;">
                    <table style="width: 100%;">
                        <tr>
                            <td><strong>SUB-TOTAL {{ $currency === 'usd' ? '($)' : '(Bs)' }}:</strong></td>
                            <td style="text-align: right;">{{ $formatNumber($order->total) }}</td>
                        </tr>
                        <tr>
                            <td><strong>TOTAL {{ $currency === 'usd' ? '($)' : '(Bs)' }}</strong></td>
                            <td style="text-align: right;">{{ $formatNumber($order->total) }}</td>
                        </tr>
                        @if($currency === 'usd')
                        <tr>
                            <td colspan="2" style="text-align: right; font-size: 0.8em; padding-top: 10px;">
                                * Montos convertidos usando tasa BCV: {{ $formatExchangeRate() }} Bs/USD
                            </td>
                        </tr>
                        @endif
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="company-instructions">
        <strong>Es importante que se cumplan las indicaciones aquí señaladas.</strong>
        <p>1.- Facturar a: SNC PHARMA, C.A. RIF: J-29855562-9, Dirección Fiscal: Centro Profesional</p>
        <p>2.- Se cancela la Orden de Compra a la TASA del Banco Central de Venezuela -</p>
    </div>

    <div style="margin-top: 60px; font-size: 0.8em;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; padding: 20px; border: none;">
                    <div style="border-top: 1px solid black; text-align: center;">
                    <div style="margin-top: 5px;"></div>
                        <div>Joel A. Lopez J.</div>
                    </div>
                </td>
                <td style="width: 50%; padding: 20px; border: none;">
                    <div style="border-top: 1px solid black; text-align: center;">
                       
                        <div>Julio H. Brandt T.</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
