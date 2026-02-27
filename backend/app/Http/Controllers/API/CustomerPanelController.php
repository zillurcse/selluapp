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
            ]
        ]);
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
                    'items' => $order->items->count(),
                    'total' => $order->total_amount,
                    'status' => ucfirst($order->status),
                ];
            });

        return response()->json(['orders' => $orders]);
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
}
