<div class="p-4 md:p-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- LISTA PROIZVODA (Zauzima 2/3 ekrana na desktopu) -->
        <div class="lg:col-span-2">
            <h2 class="text-2xl font-extrabold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
                <svg xmlns="http://www.w3.org" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="Default-M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                Available Products
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                @foreach($products as $product)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow duration-300">
                        <div class="p-5">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $product->name }}</h3>
                                <span class="bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 text-xs font-bold px-2 py-1 rounded-full">
                                    {{ number_format($product->price, 2) }}€
                                </span>
                            </div>
                            
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                In Stock: <span class="font-medium @if($product->stock_quantity < 5) text-orange-500 @endif">{{ $product->stock_quantity }}</span>
                            </p>

                            <button wire:click="addToCart({{ $product->id }})"
                                class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors duration-200 gap-2 group">
                                <svg xmlns="http://www.w3.org" class="h-4 w-4 group-hover:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add to Cart
                            </button>

                            @if(isset($stockErrors[$product->id]))
                                <p class="text-red-500 text-[10px] mt-2 text-center font-medium uppercase tracking-wider italic">
                                    {{ $stockErrors[$product->id] }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- KORPA (Zauzima 1/3 ekrana, sticky na desktopu) -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 sticky top-8">
                <div class="p-6 border-b border-gray-50 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        Your Cart
                        <span class="text-sm font-normal text-gray-400">({{ count($cartItems) }} items)</span>
                    </h2>
                </div>

                <div class="p-6 max-h-[60vh] overflow-y-auto">
                    @forelse($cartItems as $item)
                        <div class="flex items-start justify-between mb-6 last:mb-0">
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $item->product->name }}</h4>
                                <p class="text-xs text-gray-500 mt-1">{{ number_format($item->product->price, 2) }}€ / pc</p>
                                
                                @if(isset($stockErrors['cart_' . $item->id]))
                                    <p class="text-red-500 text-[10px] mt-1 font-bold italic uppercase">{{ $stockErrors['cart_' . $item->id] }}</p>
                                @endif
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="flex items-center border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 px-2">
                                    <input type="number"
                                        oninput="if(this.value > {{ $item->product->stock_quantity }}) this.value = {{ $item->product->stock_quantity }};"
                                        wire:change="updateQuantity({{ $item->id }}, $event.target.value)" 
                                        min="1"
                                        max="{{ $item->product->stock_quantity }}" 
                                        value="{{ $item->quantity }}"
                                        class="w-10 border-none bg-transparent text-center text-sm focus:ring-0 text-gray-900 dark:text-white p-1">
                                </div>
                                
                                <button wire:click="removeFromCart({{ $item->id }})" class="text-gray-300 hover:text-red-500 transition-colors">
                                    <svg xmlns="http://www.w3.org" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-gray-400 text-sm italic">Your cart is feeling lonely...</p>
                        </div>
                    @endforelse
                </div>

                <div class="p-6 bg-gray-50/50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 rounded-b-2xl">
                    <div class="flex justify-between items-center mb-4 text-gray-900 dark:text-white">
                        <span class="text-sm font-medium">Subtotal</span>
                        <span class="text-2xl font-black">{{ number_format($this->total, 2) }}€</span>
                    </div>

                    @if(count($cartItems) > 0)
                        <button wire:click="checkout"
                            class="w-full bg-green-600 hover:bg-green-700 text-white py-3.5 rounded-xl font-bold text-sm shadow-md shadow-green-200 dark:shadow-none transition-all hover:-translate-y-0.5 active:translate-y-0">
                            Proceed to Checkout
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>