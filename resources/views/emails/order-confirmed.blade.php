@component('mail::message')
# Orden de Pago #{{ $order->id }} Confirmada

La orden de pago #{{ $order->id }} ha sido confirmada por el administrador **{{ $confirmedBy->name }}**.

@if(count($pendingAdmins) > 0)
## Administradores pendientes por confirmar:
@foreach($pendingAdmins as $admin)
- {{ $admin->name }}
@endforeach
@else
Todos los administradores han confirmado esta orden.
@endif

Gracias,<br>
{{ config('app.name') }}
@endcomponent
