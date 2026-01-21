<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

use App\Jobs\SendLowStockNotification;

class ShoppingCart extends Component
{
    // Array for error messages by ID
    public $stockErrors = []; 

    public function addToCart($productId)
    {
        //find product by id
        $product = Product::findOrFail($productId);
        $this->stockErrors = []; // reset errors

        // check if adding one more exceeds stock
        $cartItem = Auth::user()->cartItems()->where('product_id', $productId)->first();
        $currentQuantity = $cartItem ? $cartItem->quantity : 0;

        //if adding one more exceeds stock, set error and return
        if (($currentQuantity + 1) > $product->stock_quantity) {
            $this->stockErrors[$productId] = "Nema više zaliha (max: {$product->stock_quantity}).";
            return;
        }

        // add to cart or increment quantity
        if ($cartItem) {
            $cartItem->increment('quantity');
        } else {
            Auth::user()->cartItems()->create([
                'product_id' => $productId,
                'quantity' => 1
            ]);
        }
    }

    // Update quantity with stock checks
    public function updateQuantity($itemId, $newQuantity)
    {
        //find cart item by id
        $cartItem = Auth::user()->cartItems()->findOrFail($itemId);
        $product = $cartItem->product;
        $this->stockErrors = [];

        // 1. If new quantity exceeds stock
        if ($newQuantity > $product->stock_quantity) {
            $this->stockErrors['cart_' . $itemId] = "Max dostupno: {$product->stock_quantity}";
            $cartItem->update(['quantity' => $product->stock_quantity]);
        }
        // 2. if new quantity is zero or less
        elseif ($newQuantity <= 0) {
            $this->removeFromCart($itemId);
        }
        // 3. valid quantity
        else {
            $cartItem->update(['quantity' => $newQuantity]);
        }

        // re-render component to reflect changes         
        $this->render();
    }

    // Remove item from cart
    public function removeFromCart($itemId)
    {
        //delete cart item by id
        Auth::user()->cartItems()->where('id', $itemId)->delete();
    }

    // Calculate total price
    public function getTotalProperty()
    {
        //sum price * quantity for all cart items
        return Auth::user()->cartItems->sum(fn($item) => $item->product->price * $item->quantity);
    }


    // Checkout process
    public function checkout()
    {
        // get user cart items with products
        $user = Auth::user();
        $items = $user->cartItems()->with('product')->get();
        $lowStockProducts = []; // array for low stock products
        $lowStockThreshold = 3; // define low stock threshold

        if ($items->isEmpty()) return;

        // Process each cart item
        foreach ($items as $item) {

            $product = $item->product;
            // Check stock availability
            if ($product->stock_quantity >= $item->quantity) {

                // 1. Write order record
                \App\Models\Order::create([
                    'user_id' => $user->id,
                    'product_name' => $product->name,
                    'price_at_purchase' => $product->price,
                    'quantity' => $item->quantity,
                ]);

                // 2. Lessen stock
                $product->decrement('stock_quantity', $item->quantity);

                // 3. Check for low stock
                if ($product->stock_quantity < $lowStockThreshold) {
                    $lowStockProducts[] = $product;
                }
            } else {
                // Not enough stock for this product
                session()->flash('error', "Not enough stock for this product: {$product->name}.");
                return;
            }
        }

        // 3. If any products are low in stock, dispatch notification job
        if (!empty($lowStockProducts)) {
            // Dispatch notification job - send email to admin
            SendLowStockNotification::dispatch($lowStockProducts);
        }

        // 4. Clear user's cart
        $user->cartItems()->delete();

        session()->flash('message', 'Successfully purchased!');
    }

    public function render()
    {
        //return view with products and user's cart items
        return view('livewire.shopping-cart', [
            'products' => Product::all(),
            'cartItems' => Auth::user()->cartItems()->with('product')->get()
        ]);
    }
}
