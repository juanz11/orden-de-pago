<x-mail::message>
# Nueva Orden de Pago Creada

**Número de Orden:** {{ $order->id }}

**Solicitante:** {{ $order->user->name }}

**Departamento:** {{ $order->user->department }}

**Proveedor:** {{ $order->supplier ? $order->supplier->name : $order->other_supplier }}

**Fecha:** {{ $order->created_at->format('d/m/Y') }}

## Items de la Orden

<x-mail::table>
| Descripción | Cantidad | Precio Unitario | Total |
|:------------|:--------:|:---------------:|:-----:|
@foreach($order->items as $item)
| {{ $item->description }} | {{ $item->quantity }} | {{ number_format($item->unit_price, 2, ',', '.') }} Bs. | {{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }} Bs. |
@endforeach
</x-mail::table>

**Total:** {{ number_format($order->total, 2, ',', '.') }} Bs.

@if(isset($approvalToken))
<x-mail::button :url="route('orders.approve-by-email', ['token' => $approvalToken->token])">
Aprobar Orden
</x-mail::button>

<small>Este enlace de aprobación expirará en 24 horas por razones de seguridad.</small>
@endif

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
