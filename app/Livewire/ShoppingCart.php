<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

use App\Jobs\SendLowStockNotification;

class ShoppingCart extends Component
{
    public $stockErrors = []; // Niz za greške po ID-u

    public function addToCart($productId)
    {
        $product = Product::findOrFail($productId);
        $this->stockErrors = []; // reset errors

        $cartItem = Auth::user()->cartItems()->where('product_id', $productId)->first();
        $currentQuantity = $cartItem ? $cartItem->quantity : 0;

        if (($currentQuantity + 1) > $product->stock_quantity) {
            $this->stockErrors[$productId] = "Nema više zaliha (max: {$product->stock_quantity}).";
            return;
        }

        if ($cartItem) {
            $cartItem->increment('quantity');
        } else {
            Auth::user()->cartItems()->create([
                'product_id' => $productId,
                'quantity' => 1
            ]);
        }

        // Added: Check low lvl state after checking stock level
        //$this->checkStockLevel($product);
    }

    public function updateQuantity($itemId, $newQuantity)
    {
        $cartItem = Auth::user()->cartItems()->findOrFail($itemId);
        $product = $cartItem->product;
        $this->stockErrors = [];

        // 1. Ako je ukucano više nego što ima na stanju
        if ($newQuantity > $product->stock_quantity) {
            $this->stockErrors['cart_' . $itemId] = "Max dostupno: {$product->stock_quantity}";
            $cartItem->update(['quantity' => $product->stock_quantity]);
        }
        // 2. Ako je ukucana nula ili negativan broj
        elseif ($newQuantity <= 0) {
            $this->removeFromCart($itemId);
        }
        // 3. Regularan unos
        else {
            $cartItem->update(['quantity' => $newQuantity]);
        }

        // Added: Check low lvl state after checking stock level
        //$this->checkStockLevel($product);

        // OVO JE KLJUČ: Prisilno osvežavamo kolekciju iz baze podataka 
        // kako bi Blade dobio novu vrednost $item->quantity
        $this->render();
    }

    public function removeFromCart($itemId)
    {
        Auth::user()->cartItems()->where('id', $itemId)->delete();
    }

    public function getTotalProperty()
    {
        return Auth::user()->cartItems->sum(fn($item) => $item->product->price * $item->quantity);
    }

    

    public function checkout()
    {
        $user = Auth::user();
        $items = $user->cartItems()->with('product')->get();
        $lowStockProducts = []; // Niz za prikupljanje kritičnih proizvoda
        $lowStockThreshold = 3;

        foreach ($items as $item) {
            $product = $item->product;

            if ($product->stock_quantity >= $item->quantity) {
                $product->decrement('stock_quantity', $item->quantity);

                // Proveri da li je proizvod pao ispod limita nakon prodaje
                if ($product->stock_quantity < $lowStockThreshold) {
                    $lowStockProducts[] = $product;
                }
            } else {
                session()->flash('error', "Nema dovoljno zaliha za {$product->name}.");
                return;
            }
        }

        // 3. Ako ima proizvoda sa niskim zalihama, pošalji JEDAN Job sa celim nizom
        if (!empty($lowStockProducts)) {
            SendLowStockNotification::dispatch($lowStockProducts);
        }

        $user->cartItems()->delete();
        session()->flash('message', 'Kupovina uspešna!');
    }

    public function render()
    {
        return view('livewire.shopping-cart', [
            'products' => Product::all(),
            'cartItems' => Auth::user()->cartItems()->with('product')->get()
        ]);
    }
}
