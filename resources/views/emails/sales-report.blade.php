@component('mail::message')
# Sales report for date: {{ now()->format('d.m.Y') }}

Overview of sold items:

| Proizvod | Količina | Cena (Ukupno) |
| :--- | :---: | :--- |
@foreach($sales as $sale)
Product: {{ $sale->product_name }} | quantity: {{ $sale->total_quantity }} | total price: {{ number_format($sale->total_price, 2) }}€ |
@endforeach

**Total earnings today: {{ number_format($totalRevenue, 2) }}€**

@component('mail::button', ['url' => config('app.url')])
Go to the website
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent