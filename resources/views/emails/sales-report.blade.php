@component('mail::message')
# Dnevni izveštaj o prodaji za datum: {{ now()->format('d.m.Y') }}

Pregled prodatih artikala:

| Proizvod | Količina | Cena (Ukupno) |
| :--- | :---: | :--- |
@foreach($sales as $sale)
| {{ $sale->product_name }} | {{ $sale->total_quantity }} | {{ number_format($sale->total_price, 2) }}€ |
@endforeach

**Ukupna zarada danas: {{ number_format($totalRevenue, 2) }}€**

@component('mail::button', ['url' => config('app.url')])
Idi na sajt
@endcomponent

Hvala,<br>
{{ config('app.name') }}
@endcomponent