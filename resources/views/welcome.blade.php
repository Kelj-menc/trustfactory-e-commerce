<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>TrustFactory Shop</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-50 dark:bg-gray-900">
        
        <!-- TOP NAV (Login/Register) -->
        <nav class="p-6 text-right">
            @if (Route::has('login'))
                <div class="space-x-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ms-4 font-semibold text-white bg-blue-600 px-4 py-2 rounded-lg hover:bg-blue-700">Register</a>
                        @endif
                    @endauth
                </div>
            @endif
        </nav>

        <div class="max-w-7xl mx-auto px-6 pb-12">
            <!-- HERO SEKCIJA -->
            <div class="text-center py-12">
                <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white sm:text-6xl mb-4">
                    Welcome to <span class="text-blue-600">TrustFactory</span>
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    Browse our premium selection of products. To start shopping and manage your cart, please log in to your account.
                </p>
            </div>

            <!-- GRID PROIZVODA -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $product->name }}</h3>
                                <span class="text-blue-600 font-black">{{ number_format($product->price, 2) }}€</span>
                            </div>
                            
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                                Stock: <span class="font-medium">{{ $product->stock_quantity }} available</span>
                            </p>

                            <!-- LOGIN button -->
                            <a href="{{ route('login') }}" 
                               class="w-full inline-flex justify-center items-center px-4 py-2.5 border-2 border-blue-600 text-blue-600 hover:bg-blue-600 hover:text-white text-sm font-bold rounded-xl transition-all duration-200">
                                Log in to Buy
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </body>
</html>