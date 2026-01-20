@component('mail::message')
# Niska Zaliha Proizvoda: {{ $product->name }}

Zdravo,

{{-- {{ $product }} je objekat koji se šalje iz Notifikacije (vidi below) --}}

{{-- {!! $product->name !!} za prikaz čiste vrednosti, koristi se ako je potrebna HTML enkapsulacija --}}

Obaveštavamo vas da je zaliha proizvoda **{{ $product->name }}** niska. Trenutno je na stanju samo **{{ $stock_level }}** komada.

@component('mail::table')
| Proizvod | Trenutna Zaliha | Minimalna Zaliha |
| :--- | :--- | :--- |
| {{ $product->name }} | {{ $stock_level }} | {{ $min_stock_level }} |
@endcomponent

@component('mail::button', ['url' => route('products.show', $product)])
Pregled Proizvoda
@endcomponent

Hvala,
Vaš Tim
@endcomponent