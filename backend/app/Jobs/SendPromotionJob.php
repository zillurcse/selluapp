<?php

namespace App\Jobs;

use App\Models\Promotion;
use App\Models\NewsletterSubscription;
use App\Mail\ShopMail;
use App\Helpers\EmailHelper;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPromotionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Promotion $promotion)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $vendorId = $this->promotion->vendor_id;
        EmailHelper::setMailConfig($vendorId);

        $subscribers = NewsletterSubscription::where('vendor_id', $vendorId)
            ->where('is_active', true)
            ->get();

        $discountStr = $this->promotion->discount_unit == 'percentage' 
            ? $this->promotion->discount_value . '%' 
            : '৳' . $this->promotion->discount_value;

        foreach ($subscribers as $subscriber) {
            $data = [
                'shop_name' => $this->promotion->vendor->name ?? config('app.name'),
                'promotion_title' => $this->promotion->title,
                'discount' => $discountStr,
                'promotion_content' => $this->promotion->type == 'coupon' ? "Use coupon code: " . strtoupper($this->promotion->slug) : "Check out our latest flash sale!",
                'shop_url' => config('app.front_url') ?? config('app.url')
            ];

            Mail::to($subscriber->email)->send(new ShopMail($vendorId, 'newsletter_promotion', $data));
            
            // Update last sent
            $subscriber->update(['last_email_sent_at' => now()]);
        }
    }
}
