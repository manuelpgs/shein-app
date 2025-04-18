@component('mail::message')
# Your Shein Package is Ready for Pickup!

Dear {{ $order->client->name }},

Your package for Shein order number **{{ $sheinOrderNumber }}** is now available for pickup at our office.

Please come and collect it at your earliest convenience.

Thank you for your patience!

Regards and gracias totales v2!<br>,
{{ config('app.name') }}
@endcomponent