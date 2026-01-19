<div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}

    {{-- LISTA PROIZVODA --}}
    <div class="bg-white p-4 shadow rounded-lg">
        <h2 class="text-xl font-bold mb-4">Dostupni Proizvodi</h2>

        @if(session()->has('error'))
            <div class="bg-red-100 text-red-700 p-2 mb-4 rounded">{{ session('error') }}</div>
        @endif

        <div class="space-y-4">
            @foreach($products as $product)
                <div class="flex justify-between items-center border-b pb-2">
                    <div>
                        <span class="font-semibold">{{ $product->name }}</span>
                        <p class="text-sm text-gray-500">Cena: {{ $product->price }}€ | Zalihe:
                            {{ $product->stock_quantity }}</p>
                    </div>
                    <button wire:click="addToCart({{ $product->id }})"
                        class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700">
                        Dodaj u korpu
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    {{-- KORPA --}}
    <div class="bg-gray-50 p-4 shadow rounded-lg">
        <h2 class="text-xl font-bold mb-4">Vaša Korpa</h2>

        @if(count($cartItems) > 0)
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b">
                        <th class="py-2">Proizvod</th>
                        <th class="py-2 text-center">Količina</th>
                        <th class="py-2">Cena</th>
                        <th class="py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cartItems as $item)
                        <tr class="border-b">
                            <td class="py-3">{{ $item->product->name }}</td>
                            <td class="py-3 text-center">
                                <input type="number" wire:change="updateQuantity({{ $item->id }}, $event.target.value)"
                                    value="{{ $item->quantity }}" class="w-16 border rounded text-center">
                            </td>
                            <td class="py-3">{{ $item->product->price * $item->quantity }}€</td>
                            <td class="py-3 text-right">
                                <button wire:click="removeFromCart({{ $item->id }})" class="text-red-500 font-bold">X</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4 text-right">
                <p class="text-lg font-bold">Ukupno: {{ $this->total }}€</p>
            </div>
        @else
            <p class="text-gray-500">Korpa je prazna.</p>
        @endif
    </div>
</div>