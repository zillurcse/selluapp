<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\SmsService;
use App\Models\ShopSetting;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // If order is NOT pending_verification, it means it's ready to be notified as "placed"
        // If it IS pending_verification, we wait until it's "pending" (verified) in the updated event.
        if ($order->status !== 'pending_verification') {
            $this->sendOrderPlacedSms($order);
        }
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->isDirty('status')) {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;

            // Transition from pending_verification to pending means it was just verified
            if ($oldStatus === 'pending_verification' && $newStatus === 'pending') {
                $this->sendOrderPlacedSms($order);
            } 
            // If moved to courier, send shipping update
            elseif ($newStatus === 'courier') {
                $this->sendOrderShippedSms($order);
            }
            // Other status changes (excluding pending/processing which might be internal noise)
            elseif (!in_array($newStatus, ['pending_verification', 'pending'])) {
                 $this->sendStatusChangeSms($order);
            }
        }
    }

    protected function sendOrderPlacedSms(Order $order)
    {
        $this->notify($order, 'order_placed');
    }

    protected function sendOrderShippedSms(Order $order)
    {
        $this->notify($order, 'order_shipped');
    }

    protected function sendStatusChangeSms(Order $order)
    {
        $this->notify($order, 'order_status_change');
    }

    protected function notify(Order $order, $type)
    {
        try {
            $vendorId = $order->user_id;
            $shippingInfo = json_decode($order->shipping_address, true);
            $phone = $shippingInfo['phone'] ?? null;

            if (!$phone) return;

            $smsService = new SmsService($vendorId);
            $shopName = ShopSetting::where('user_id', $vendorId)->where('key', 'shop_name')->first()?->value ?? 'Our Shop';

            $data = [
                'order_id' => $order->invoice_number,
                'amount' => '৳' . number_format($order->total_amount, 2),
                'shop_name' => $shopName,
                'status' => ucwords(str_replace('_', ' ', $order->status)),
            ];

            // Add courier info if available
            if ($type === 'order_shipped') {
                $trackingParts = explode(':', $order->tracking_number ?? '', 2);
                $data['courier'] = count($trackingParts) == 2 ? $trackingParts[0] : ($order->courier_name ?? 'Courier');
                $data['tracking_id'] = count($trackingParts) == 2 ? $trackingParts[1] : ($order->tracking_number ?? 'N/A');
            }

            Log::info("OrderObserver: Sending {$type} SMS for Order #{$order->invoice_number} to {$phone}");
            $smsService->send($phone, $type, $data);
            
        } catch (\Exception $e) {
            Log::error("OrderObserver SMS Error: " . $e->getMessage());
        }
    }
}
