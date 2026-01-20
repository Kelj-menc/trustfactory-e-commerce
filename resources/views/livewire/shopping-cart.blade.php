<div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
    {{-- To attain knowledge, add things every day; To attain wisdom, subtract things every day. --}}

    <!-- LISTA PROIZVODA -->
    <div class="bg-white p-4 shadow rounded-lg">
        <h2 class="text-xl font-bold mb-4">Dostupni Proizvodi</h2>

        <!-- @if(session()->has('error'))
        <div class="bg-red-100 text-red-700 p-2 mb-4 rounded">{{ session('error') }}</div>
        @endif -->

        <div class="space-y-4">
            @foreach($products as $product)
                <div class="flex justify-between items-center border-b pb-2">
                    <div>
                        <span class="font-semibold">{{ $product->name }}</span>
                        <p class="text-sm text-gray-500">Cena: {{ $product->price }}€ | Zalihe:
                            {{ $product->stock_quantity }}
                        </p>
                    </div>
                    <button wire:click="addToCart({{ $product->id }})"
                        class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700">
                        Dodaj u korpu
                    </button>
                    {{-- Greška za specifičan proizvod --}}
                    @if(isset($stockErrors[$product->id]))
                        <p class="text-red-500 text-xs mt-1">{{ $stockErrors[$product->id] }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- KORPA --}}
    <div class="bg-gray-50 p-4 shadow rounded-lg">
        <h2 class="text-xl font-bold mb-4">Vaša Korpa</h2>
        @foreach($cartItems as $item)
            <div class="flex items-center justify-between border-b py-3">
                <div class="flex-1">
                    <p class="font-medium">{{ $item->product->name }}</p>
                    <p class="text-sm text-gray-600">{{ $item->product->price * $item->quantity }}€</p>

                    {{-- Greška za stavku u korpi --}}
                    @if(isset($stockErrors['cart_' . $item->id]))
                        <p class="text-red-500 text-xs">{{ $stockErrors['cart_' . $item->id] }}</p>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <input type="number"
                        oninput="if(this.value > {{ $item->product->stock_quantity }}) this.value = {{ $item->product->stock_quantity }};"
                        wire:change="updateQuantity({{ $item->id }}, $event.target.value)" min="1"
                        max="{{ $item->product->stock_quantity }}" value="{{ $item->quantity }}"
                        class="w-16 border rounded text-center text-sm">
                    <button wire:click="removeFromCart({{ $item->id }})" class="text-red-400 ml-2">X</button>
                </div>
            </div>
        @endforeach
        <div class="mt-4 text-right font-bold">Ukupno: {{ $this->total }}€</div>

        <!-- checkout button -->
        @if(count($cartItems) > 0)
            <button wire:click="checkout"
                class="w-full mt-4 bg-green-600 text-white py-2 rounded-lg font-bold hover:bg-green-700">
                Završi kupovinu (Checkout)
            </button>
        @endif
    </div>
</div>