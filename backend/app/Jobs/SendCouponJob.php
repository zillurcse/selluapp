<?php

namespace App\Jobs;

use App\Models\Coupon;
use App\Models\NewsletterSubscription;
use App\Mail\ShopMail;
use App\Helpers\EmailHelper;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCouponJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Coupon $coupon)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $vendorId = $this->coupon->vendor_id;
        EmailHelper::setMailConfig($vendorId);

        $subscribers = NewsletterSubscription::where('vendor_id', $vendorId)
            ->where('is_active', true)
            ->get();

        $discountStr = $this->coupon->discount_type == 'percentage' 
            ? $this->coupon->discount_amount . '%' 
            : '৳' . $this->coupon->discount_amount;

        foreach ($subscribers as $subscriber) {
            $data = [
                'shop_name' => $this->coupon->vendor->name ?? config('app.name'),
                'coupon_title' => $this->coupon->title,
                'discount' => $discountStr,
                'coupon_code' => strtoupper($this->coupon->code),
                'shop_url' => config('app.front_url') ?? config('app.url')
            ];

            Mail::to($subscriber->email)->send(new ShopMail($vendorId, 'newsletter_coupon', $data));
            
            // Update last sent
            $subscriber->update(['last_email_sent_at' => now()]);
        }
    }
}
