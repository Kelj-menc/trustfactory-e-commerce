@component('mail::message')
# Upozorenje o niskim zalihama

Sledeći proizvodi su pali ispod limita:

@foreach($products as $product)
* **{{ $product->name }}** (Preostalo: {{ $product->stock_quantity }})
@endforeach

Molimo vas da dopunite zalihe.

<!-- @component('mail::button', ['url' => url('/admin/inventory')])
Idi na Upravljanje Zalihama
@endcomponent -->

Hvala,<br>
{{ config('app.name') }}
@endcomponent