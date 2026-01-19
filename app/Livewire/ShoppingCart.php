<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class ShoppingCart extends Component
{
    public $cart = [];

    // Učitava korpu iz sesije čim se komponenta pokrene
    public function mount()
    {
        $this->cart = session()->get('cart', []);
    }

    public function addToCart($productId)
    {
        $product = Product::findOrFail($productId);

        if ($product->stock_quantity <= 0) {
            session()->flash('error', 'Proizvod nije na stanju.');
            return;
        }

        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['quantity'] < $product->stock_quantity) {
                $this->cart[$productId]['quantity']++;
            } else {
                session()->flash('error', 'Nema više zaliha.');
                return;
            }
        } else {
            $this->cart[$productId] = [
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1
            ];
        }

        $this->save();
    }

    public function updateQuantity($productId, $newQuantity)
    {
        $product = Product::find($productId);

        if ($newQuantity > 0 && $newQuantity <= $product->stock_quantity) {
            $this->cart[$productId]['quantity'] = $newQuantity;
        } elseif ($newQuantity <= 0) {
            $this->removeFromCart($productId);
        }

        $this->save();
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
        $this->save();
    }

    private function save()
    {
        session()->put('cart', $this->cart);
    }

    public function getTotalProperty()
    {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    }
    public function render()
    {
        return view('livewire.shopping-cart', [
            'products' => Product::all()
        ]);
    }
}
