<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\Offers\OfferCalculationService;

class CartController extends Controller
{
    protected OfferCalculationService $offerService;

    public function __construct(OfferCalculationService $offerService)
    {
        $this->offerService = $offerService;
    }

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
                'price' => $product->sale_price ?? $product->price, // The base price
                'image' => $product->image ? Storage::disk('public')->url($product->image) : null,
                'quantity' => $cart->quantity,
                'product_id' => $product->id,
                'vendor_id' => $product->vendor_id,
            ];
        })->filter()->values()->toArray();

        // Currently, we run this per vendor or overall. Assuming typical ecommerce, 
        // offers might be specific to the vendor or admin. The Cart items might belong to MULTIPLE vendors.
        // For standard marketplace, we should probably group by vendor. For now, running globally:
        
        // Ensure format is an array of raw item arrays suitable for calculation
        $calculationResult = $this->offerService->calculateDiscounts($formatted);

        return response()->json([
            'data' => $calculationResult['items'], 
            'summary' => [
                'original_total' => $calculationResult['original_total'],
                'discount_total' => $calculationResult['discount_total'],
                'final_total' => $calculationResult['final_total'],
                'applied_offers' => $calculationResult['applied_offers']
            ]
        ]);
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
