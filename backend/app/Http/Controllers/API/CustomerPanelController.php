<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Order;
use App\Models\Customer;

class CustomerPanelController extends Controller
{
    private function getCustomer(Request $request)
    {
        return Customer::where('email', $request->user()->email)->first();
    }

    public function dashboard(Request $request)
    {
        $customer = $this->getCustomer($request);
        
        $totalOrders = 0;
        $totalSpent = 0;
        $recentOrders = collect();

        if ($customer) {
            $totalOrders = Order::where('customer_id', $customer->id)->count();
            $totalSpent = collect(\DB::select("SELECT SUM(total_amount) as total FROM orders WHERE customer_id = ? AND status != 'cancelled'", [$customer->id]))->first()->total ?? 0;
            $recentOrders = Order::where('customer_id', $customer->id)
                ->with('items')
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get()
                ->map(function($order) {
                    return [
                        'id' => $order->id,
                        'invoice_number' => $order->invoice_number,
                        'icon' => '📦',
                        'name' => 'Order #' . $order->invoice_number,
                        'date' => $order->created_at->format('M d, Y'),
                        'items' => $order->items->count(),
                        'total' => $order->total_amount,
                        'status' => ucfirst($order->status),
                    ];
                });
        }

        $stats = [
            [ 'icon' => '📦', 'value' => (string)$totalOrders, 'label' => 'Total Orders' ],
            [ 'icon' => '❤️', 'value' => '4', 'label' => 'Wishlist Items' ],
            [ 'icon' => '💰', 'value' => '$' . number_format($totalSpent, 2), 'label' => 'Total Spent' ],
            [ 'icon' => '⭐', 'value' => '3', 'label' => 'Reviews Left' ],
        ];

        $wishlistItems = [
            [ 'id' => 1, 'emoji' => '🛋️', 'name' => 'Velvet Sofa', 'price' => '899.00' ],
            [ 'id' => 2, 'emoji' => '🕯️', 'name' => 'Scented Candle Set', 'price' => '45.00' ],
            [ 'id' => 3, 'emoji' => '🪴', 'name' => 'Indoor Plant Trio', 'price' => '78.00' ],
            [ 'id' => 4, 'emoji' => '🎧', 'name' => 'Noise-Cancel Headphones', 'price' => '249.00' ],
        ];

        return response()->json([
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'wishlistItems' => $wishlistItems, 
        ]);
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        $customer = $this->getCustomer($request);

        $nameParts = explode(' ', trim($user->name));
        $initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));

        return response()->json([
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'initials' => $initials,
            ],
            'profile' => [
                'firstName' => $customer ? $customer->first_name : $nameParts[0],
                'lastName' => $customer ? $customer->last_name : (isset($nameParts[1]) ? $nameParts[1] : ''),
                'email' => $user->email,
                'phone' => $customer ? $customer->phone : '',
            ],
            'settings' => $customer && $customer->settings ? $customer->settings : [
                ['id' => 1, 'label' => 'Order Updates', 'desc' => 'Get notified about your order status changes', 'enabled' => true],
                ['id' => 2, 'label' => 'New Arrivals', 'desc' => 'Be the first to know about new products', 'enabled' => false],
                ['id' => 3, 'label' => 'Newsletter', 'desc' => 'Weekly digest of best deals and stories', 'enabled' => true]
            ]
        ]);
    }

    public function updateSettings(Request $request)
    {
        $customer = $this->getCustomer($request);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $validated = $request->validate([
            'settings' => 'required|array',
        ]);

        $customer->update([
            'settings' => $validated['settings']
        ]);

        return response()->json(['message' => 'Settings updated successfully']);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();
        $customer = $this->getCustomer($request);

        if ($customer) {
            $customer->delete(); // Soft delete if implemented
        }

        $user->delete();

        return response()->json(['message' => 'Account deleted successfully']);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $customer = $this->getCustomer($request);
        
        $validated = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => trim($validated['firstName'] . ' ' . ($validated['lastName'] ?? ''))
        ]);

        if ($customer) {
            $customer->update([
                'first_name' => $validated['firstName'],
                'last_name' => $validated['lastName'],
                'phone' => $validated['phone'],
            ]);
        }

        return response()->json(['message' => 'Profile updated successfully']);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided password does not match your current password.']
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['message' => 'Password updated successfully']);
    }

    public function orders(Request $request)
    {
        $customer = $this->getCustomer($request);
        
        if (!$customer) {
            return response()->json(['orders' => []]);
        }

        $orders = Order::where('customer_id', $customer->id)
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($order) {
                return [
                    'id' => $order->id,
                    'invoice_number' => $order->invoice_number,
                    'icon' => '📦',
                    'name' => 'Order #' . $order->invoice_number,
                    'date' => $order->created_at->format('M d, Y'),
                    'items_count' => $order->items->count(),
                    'total' => $order->total_amount,
                    'status' => ucfirst($order->status),
                    'order' => $order // Return full order for easier detail viewing
                ];
            });

        return response()->json(['orders' => $orders]);
    }

    public function showOrder(Request $request, $id)
    {
        $customer = $this->getCustomer($request);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $order = Order::where('customer_id', $customer->id)
            ->with(['items.product'])
            ->findOrFail($id);

        return response()->json(['order' => $order]);
    }

    public function addresses(Request $request)
    {
        $addresses = \App\Models\CustomerAddress::where('user_id', $request->user()->id)
            ->oldest()
            ->get();
            
        return response()->json(['addresses' => $addresses]);
    }

    public function storeAddress(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'line1' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => 'required|string|max:50',
            'country' => 'required|string|max:255',
            'is_default' => 'boolean',
        ]);

        if ($validated['is_default'] ?? false) {
            \App\Models\CustomerAddress::where('user_id', $request->user()->id)->update(['is_default' => false]);
        }

        $address = \App\Models\CustomerAddress::create(array_merge($validated, [
            'user_id' => $request->user()->id,
        ]));

        return response()->json([
            'message' => 'Address saved successfully',
            'address' => $address
        ]);
    }

    public function updateAddress(Request $request, $id)
    {
        $address = \App\Models\CustomerAddress::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'line1' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => 'required|string|max:50',
            'country' => 'required|string|max:255',
            'is_default' => 'boolean',
        ]);

        if ($validated['is_default'] ?? false) {
            \App\Models\CustomerAddress::where('user_id', $request->user()->id)->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json([
            'message' => 'Address updated successfully',
            'address' => $address
        ]);
    }

    public function destroyAddress(Request $request, $id)
    {
        $address = \App\Models\CustomerAddress::where('user_id', $request->user()->id)->findOrFail($id);

        if ($address->is_default) {
            return response()->json(['message' => 'Cannot delete default address'], 422);
        }

        $address->delete();

        return response()->json(['message' => 'Address deleted successfully']);
    }

    public function submitReview(Request $request, \App\Models\Product $product)
    {
        $customer = $this->getCustomer($request);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check if reviews are enabled for this vendor
        $vendorId = $product->vendor_id;
        $reviewSettings = \App\Models\ShopSetting::where('user_id', $vendorId)
            ->where('group', 'customer_reviews')
            ->first();

        $settings = $reviewSettings ? json_decode($reviewSettings->value, true) : [
            'enableReviews' => true,
            'autoApprove' => false,
            'verifiedOnly' => true,
            'minAutoApproveRating' => '4'
        ];

        if (isset($settings['enableReviews']) && !$settings['enableReviews']) {
            return response()->json(['message' => 'Reviews are disabled for this product.'], 403);
        }

        // Check if customer purchased the product and it was delivered
        $hasPurchased = \App\Models\Order::where('customer_id', $customer->id)
            ->where('status', 'delivered')
            ->whereHas('items', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })->exists();

        if (!$hasPurchased) {
             return response()->json(['message' => 'You can only review products after purchasing and receiving them.'], 403);
        }

        $isVerified = true;

        // Determine initial status based on auto-approve settings
        $status = 'pending';
        if (isset($settings['autoApprove']) && $settings['autoApprove']) {
             $minRating = isset($settings['minAutoApproveRating']) ? (int)$settings['minAutoApproveRating'] : 4;
             if ($validated['rating'] >= $minRating) {
                 $status = 'approved';
             }
        }

        $review = \App\Models\ProductReview::create([
            'user_id' => $vendorId,
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? '',
            'status' => $status,
            'is_verified' => $isVerified,
        ]);

        return response()->json([
            'message' => 'Review submitted successfully.' . ($status === 'pending' ? ' It is pending approval.' : ''),
            'review' => $review
        ]);
    }

    public function canReview(Request $request, \App\Models\Product $product)
    {
        $customer = $this->getCustomer($request);
        if (!$customer) {
            return response()->json(['can_review' => false, 'message' => 'Please login to review.'], 403);
        }

        $vendorId = $product->vendor_id;
        $reviewSettings = \App\Models\ShopSetting::where('user_id', $vendorId)
            ->where('group', 'customer_reviews')
            ->first();

        $settings = $reviewSettings ? json_decode($reviewSettings->value, true) : [
            'enableReviews' => true,
        ];

        if (isset($settings['enableReviews']) && !$settings['enableReviews']) {
             return response()->json(['can_review' => false, 'message' => 'Reviews are disabled for this product.']);
        }

        $hasPurchased = \App\Models\Order::where('customer_id', $customer->id)
            ->where('status', 'delivered')
            ->whereHas('items', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })->exists();

        if (!$hasPurchased) {
             return response()->json(['can_review' => false, 'message' => 'You can only review products after purchasing and receiving them.']);
        }

        // Optional: Check if the user already reviewed the product
        $alreadyReviewed = \App\Models\ProductReview::where('customer_id', $customer->id)
            ->where('product_id', $product->id)
            ->exists();

        if ($alreadyReviewed) {
             return response()->json(['can_review' => false, 'message' => 'You have already reviewed this product.']);
        }

        return response()->json(['can_review' => true]);
    }
}
