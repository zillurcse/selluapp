<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\EmailTemplate;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $vendors = User::role('vendor')->get();

        $promotionContent = '
        <div style="font-family: \'Inter\', sans-serif; max-width: 600px; margin: 0 auto; padding: 40px 20px; background-color: #f4f4f5;">
            <div style="background-color: #ffffff; border-radius: 16px; padding: 40px; border: 1px solid #e4e4e7;">
                <h1 style="color: #18181b; font-size: 24px; font-weight: 800; margin-bottom: 24px;">{{ coupon_title }}</h1>
                <div style="margin-bottom: 24px;">
                    <div style="background-color: #f3f4f6; padding: 20px; border-radius: 12px; text-align: center; margin: 24px 0;">
                        <p style="margin: 0; color: #6b7280; font-size: 14px;">Here is your exclusive coupon code for <strong>{{ discount }}</strong> off:</p>
                        <h2 style="margin: 8px 0; color: #111827; font-size: 24px; font-weight: 800; letter-spacing: 2px;">{{ coupon_code }}</h2>
                    </div>
                    <p style="color: #4b5563; font-size: 16px; line-height: 1.6; text-align: center;">Use this code at checkout to claim your discount.</p>
                </div>
                <div style="text-align: center; margin-top: 32px;">
                    <a href="{{ shop_url }}" style="display: inline-block; background-color: #111827; color: #ffffff; padding: 14px 32px; border-radius: 12px; text-decoration: none; font-weight: 700;">Start Shopping</a>
                </div>
                <div style="text-align: center; margin-top: 32px; padding-top: 24px; border-top: 1px solid #f4f4f5;">
                    <p style="color: #71717a; font-size: 12px;">You received this email because you are subscribed to {{ shop_name }} newsletter.</p>
                </div>
            </div>
        </div>';

        foreach ($vendors as $vendor) {
            EmailTemplate::updateOrCreate(
                ['user_id' => $vendor->id, 'type' => 'newsletter_coupon'],
                [
                    'subject' => 'Exclusive Coupon: {{ coupon_title }}',
                    'content' => $promotionContent,
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        EmailTemplate::where('type', 'newsletter_coupon')->delete();
    }
};
