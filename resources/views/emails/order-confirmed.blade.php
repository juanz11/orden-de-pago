@component('mail::message')
# Orden de Pago #{{ $order->id }} Confirmada

La orden de pago ha sido confirmada exitosamente.

Gracias,<br>
{{ config('app.name') }}
@endcomponent
