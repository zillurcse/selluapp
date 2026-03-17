<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;
use App\Models\User;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = User::role('vendor')->get();

        $templateContent = '
        <div style="font-family: \'Inter\', -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 40px 20px; background-color: #f9fafb;">
            <div style="background-color: #ffffff; border-radius: 16px; padding: 40px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); border: 1px solid #e5e7eb;">
                <div style="text-align: center; margin-bottom: 32px;">
                    <h1 style="margin: 0; color: #111827; font-size: 24px; font-weight: 800; letter-spacing: -0.025em;">{{ shop_name }}<span style="color: #7c3aed;">.</span></h1>
                </div>
                
                <h2 style="margin: 0 0 16px; color: #111827; font-size: 20px; font-weight: 700;">Secure Login Link</h2>
                <p style="margin: 0 0 24px; color: #4b5563; font-size: 16px; line-height: 1.6;">Hello {{ name }},</p>
                <p style="margin: 0 0 32px; color: #4b5563; font-size: 16px; line-height: 1.6;">Use the secure link below to log in to your account. This link is only valid for <strong>{{ expires_in }}</strong>.</p>
                
                <div style="text-align: center; margin-bottom: 32px;">
                    <a href="{{ magic_link }}" style="display: inline-block; background-color: #111827; color: #ffffff; font-weight: 700; font-size: 15px; padding: 14px 32px; border-radius: 12px; text-decoration: none; transition: all 0.2s ease;">
                        Sign in to Dashboard
                    </a>
                </div>
                
                <p style="margin: 0 0 16px; color: #6b7280; font-size: 14px; line-height: 1.5;">If you did not request this link, you can safely ignore this email. Your account is secure.</p>
                
                <div style="margin-top: 40px; padding-top: 24px; border-top: 1px solid #f3f4f6; text-align: center;">
                    <p style="margin: 0; color: #9ca3af; font-size: 12px;">&copy; ' . date('Y') . ' {{ shop_name }}. All rights reserved.</p>
                </div>
            </div>
            <div style="text-align: center; margin-top: 24px;">
                <p style="margin: 0; color: #9ca3af; font-size: 12px;">This is an automated message. Please do not reply to this email.</p>
            </div>
        </div>';

        foreach ($vendors as $vendor) {
            EmailTemplate::updateOrCreate(
                [
                    'user_id' => $vendor->id,
                    'type'    => 'magic_link_login',
                ],
                [
                    'subject'   => 'Your Secure Login Link for {{ shop_name }}',
                    'content'   => $templateContent,
                    'is_active' => true,
                ]
            );
        }
    }
}
