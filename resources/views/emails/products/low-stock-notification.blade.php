@component('mail::message')
# Niska Zaliha Proizvoda: {{ $product->name }}

Zdravo,

{{-- {{ $product }} je objekat koji se šalje iz Notifikacije (vidi below) --}}

{{-- {!! $product->name !!} za prikaz čiste vrednosti, koristi se ako je potrebna HTML enkapsulacija --}}

Obaveštavamo vas da je zaliha proizvoda **{{ $product->name }}** niska. Trenutno je na stanju samo **{{ $product->stock_quantity }}** komada.

@component('mail::table')
| Proizvod | Trenutna Zaliha |
| :--- | :--- |
| {{ $product->name }} | {{ $product->stock_quantity }} |
@endcomponent



Hvala,
Vaš Tim
@endcomponent