<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CartController extends Controller
{
    public function index()
    {
        $carts = Cart::with('product')->where('user_id', Auth::id())->get();

        $formatted = $carts->map(function ($cart) {
            $product = $cart->product;
            if (!$product) return null;

            return [
                'id' => $cart->product_id, // frontend uses product id as item id
                'cart_id' => $cart->id,
                'name' => $product->name,
                'price' => $product->sale_price ?? $product->price,
                'image' => $product->image ? Storage::disk('public')->url($product->image) : null,
                'quantity' => $cart->quantity
            ];
        })->filter()->values();

        return response()->json(['data' => $formatted]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::firstOrNew([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id
        ]);

        $cart->quantity = $cart->exists ? $cart->quantity + $request->quantity : $request->quantity;
        $cart->save();

        return response()->json(['message' => 'Added to cart', 'data' => $cart]);
    }

    public function update(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::where('user_id', Auth::id())->where('product_id', $productId)->firstOrFail();
        $cart->quantity = $request->quantity;
        $cart->save();

        return response()->json(['message' => 'Cart updated', 'data' => $cart]);
    }

    public function destroy($productId)
    {
        Cart::where('user_id', Auth::id())->where('product_id', $productId)->delete();
        return response()->json(['message' => 'Removed from cart']);
    }
}
