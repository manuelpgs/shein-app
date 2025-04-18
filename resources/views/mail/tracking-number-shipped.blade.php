@component('mail::message')
# Your Shein Order Shipped!

Dear {{ $order->client->name }},

Your order with Shein order number **{{ $sheinOrderNumber }}** has been shipped!

Your tracking number is: **{{ $trackingNumber }}**

You can use this number to track your package on the shipping provider's website.

Thank you for your business!

Regards and gracias totales!<br>,
{{ config('app.name') }}
@endcomponent