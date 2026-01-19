<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class ShoppingCart extends Component
{
    public function addToCart($productId)
    {
        $product = Product::findOrFail($productId);

        // Provera zaliha
        if ($product->stock_quantity <= 0) {
            session()->flash('error', 'Nema na stanju.');
            return;
        }

        // Pronađi stavku u bazi za trenutnog korisnika
        $cartItem = Auth::user()->cartItems()->where('product_id', $productId)->first();

        if ($cartItem) {
            if ($cartItem->quantity < $product->stock_quantity) {
                $cartItem->increment('quantity');
            } else {
                session()->flash('error', 'Nema više zaliha.');
            }
        } else {
            Auth::user()->cartItems()->create([
                'product_id' => $productId,
                'quantity' => 1
            ]);
        }
    }

    public function updateQuantity($itemId, $newQuantity)
    {
        $cartItem = Auth::user()->cartItems()->findOrFail($itemId);
        $product = $cartItem->product;

        if ($newQuantity > 0 && $newQuantity <= $product->stock_quantity) {
            $cartItem->update(['quantity' => $newQuantity]);
        } elseif ($newQuantity <= 0) {
            $cartItem->delete();
        }
    }

    public function removeFromCart($itemId)
    {
        Auth::user()->cartItems()->where('id', $itemId)->delete();
    }

    public function getTotalProperty()
    {
        return Auth::user()->cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });
    }

    public function render()
    {
        return view('livewire.shopping-cart', [
            'products' => Product::all(),
            'cartItems' => Auth::user()->cartItems()->with('product')->get()
        ]);
    }
}
