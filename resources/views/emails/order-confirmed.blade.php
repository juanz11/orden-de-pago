<x-mail::message>
# ✅ Orden Confirmada por 3 Usuarios

**¡La orden ha sido completamente aprobada!**

**Número de Orden:** {{ $order->id }}

**Solicitante:** {{ $order->user->name ?? 'No disponible' }}

**Departamento:** {{ $order->user->department ?? 'No disponible' }}

**Proveedor:** {{ $order->supplier ? $order->supplier->name : ($order->other_supplier ?? 'No disponible') }}

**Fecha de Creación:** {{ $order->created_at->format('d/m/Y') }}

**Fecha de Confirmación:** {{ now()->format('d/m/Y H:i') }}

## Resumen de la Orden

<x-mail::table>
| Descripción | Cantidad | Precio Unitario | Total |
|:------------|:--------:|:---------------:|:-----:|
@foreach($order->items as $item)
| {{ $item->description }} | {{ $item->quantity }} | {{ number_format($item->unit_price, 2, ',', '.') }} Bs. | {{ number_format($item->quantity * $item->unit_price, 2, ',', '.') }} Bs. |
@endforeach
</x-mail::table>

**Total:** {{ number_format($order->total, 2, ',', '.') }} Bs.

## Usuarios que Aprobaron

@php
    $approvedApprovals = $order->approvals()->where('status', 'aprobado')->with('user')->get();
@endphp

@foreach($approvedApprovals as $approval)
- **{{ $approval->user->name ?? 'Usuario no disponible' }}** ({{ $approval->user->role ?? 'Rol no disponible' }}) - Aprobado el {{ $approval->approved_at ? $approval->approved_at->format('d/m/Y H:i') : 'Fecha no disponible' }}
@endforeach

**Estado Actual:** ✅ **Orden Confirmada**

La orden ha recibido las 3 aprobaciones requeridas y ahora está lista para el siguiente paso del proceso.

<x-mail::button :url="route('orders.show', ['order' => $order->id])">
Ver Orden
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>