@component('mail::message')
# Warning about low stock of item/s

The following products fell below the limit:

@foreach($products as $product)
* **{{ $product->name }}** (Left: {{ $product->stock_quantity }})
@endforeach

Please restock.

Thanks,<br>
{{ config('app.name') }}
@endcomponent