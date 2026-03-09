<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            [
                'name' => 'Cash on Delivery',
                'slug' => 'cod',
                'icon' => 'Banknote',
                'description' => 'Accept payments at your doorstep.',
                'is_active' => true,
                'config_fields' => []
            ],
            [
                'name' => 'Stripe',
                'slug' => 'stripe',
                'icon' => 'CreditCard',
                'description' => 'Secure worldwide card payments.',
                'is_active' => true,
                'config_fields' => [
                    ['name' => 'publicKey', 'label' => 'Publishable Key', 'type' => 'text', 'placeholder' => 'pk_test_...'],
                    ['name' => 'secretKey', 'label' => 'Secret Key', 'type' => 'password', 'placeholder' => 'sk_test_...'],
                    ['name' => 'webhookSecret', 'label' => 'Webhook Secret', 'type' => 'password', 'placeholder' => 'whsec_...'],
                ]
            ],
            [
                'name' => 'bKash',
                'slug' => 'bkash',
                'icon' => 'Wallet',
                'description' => 'Accept payments via bKash Personal/Agent/Merchant.',
                'is_active' => true,
                'config_fields' => [
                    ['name' => 'number', 'label' => 'bKash Number', 'type' => 'text', 'placeholder' => '01XXXXXXXXX'],
                    ['name' => 'type', 'label' => 'Account Type', 'type' => 'select', 'options' => ['Personal', 'Agent', 'Merchant']],
                ]
            ],
            [
                'name' => 'Nagad',
                'slug' => 'nagad',
                'icon' => 'Wallet',
                'description' => 'Accept payments via Nagad Personal/Agent/Merchant.',
                'is_active' => true,
                'config_fields' => [
                    ['name' => 'number', 'label' => 'Nagad Number', 'type' => 'text', 'placeholder' => '01XXXXXXXXX'],
                    ['name' => 'type', 'label' => 'Account Type', 'type' => 'select', 'options' => ['Personal', 'Agent', 'Merchant']],
                ]
            ],
            [
                'name' => 'Rocket',
                'slug' => 'rocket',
                'icon' => 'Wallet',
                'description' => 'Accept payments via Rocket Personal/Agent/Merchant.',
                'is_active' => true,
                'config_fields' => [
                    ['name' => 'number', 'label' => 'Rocket Number', 'type' => 'text', 'placeholder' => '01XXXXXXXXX'],
                    ['name' => 'type', 'label' => 'Account Type', 'type' => 'select', 'options' => ['Personal', 'Agent', 'Merchant']],
                ]
            ],
            [
                'name' => 'Manual Bank Transfer',
                'slug' => 'manual-bank',
                'icon' => 'Banknote',
                'description' => 'Direct bank transfer with manual verification.',
                'is_active' => true,
                'config_fields' => [
                    ['name' => 'bank_name', 'label' => 'Bank Name', 'type' => 'text', 'placeholder' => 'e.g. Dutch Bangla Bank'],
                    ['name' => 'account_name', 'label' => 'Account Name', 'type' => 'text', 'placeholder' => 'e.g. John Doe'],
                    ['name' => 'account_number', 'label' => 'Account Number', 'type' => 'text', 'placeholder' => 'e.g. 1012XXXXXXXX'],
                    ['name' => 'branch_name', 'label' => 'Branch Name', 'type' => 'text', 'placeholder' => 'e.g. Banani Branch'],
                    ['name' => 'instruction', 'label' => 'Payment Instructions', 'type' => 'textarea', 'placeholder' => 'Additional instructions for the customer...'],
                ]
            ],
            [
                'name' => 'Instant Bank Transfer',
                'slug' => 'bank',
                'icon' => 'Zap',
                'description' => 'Real-time bank verification.',
                'is_active' => true,
                'config_fields' => [
                    ['name' => 'bankName', 'label' => 'Bank Name', 'type' => 'text', 'placeholder' => 'e.g. Dutch Bangla Bank'],
                    ['name' => 'accountName', 'label' => 'Account Name', 'type' => 'text', 'placeholder' => 'e.g. John Doe'],
                    ['name' => 'accountNumber', 'label' => 'Account Number', 'type' => 'text', 'placeholder' => 'e.g. 1012XXXXXXXX'],
                    ['name' => 'branchName', 'label' => 'Branch Name', 'type' => 'text', 'placeholder' => 'e.g. Banani Branch'],
                    ['name' => 'routingNumber', 'label' => 'Routing Number (Optional)', 'type' => 'text', 'placeholder' => 'Routing Number'],
                ]
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::updateOrCreate(['slug' => $method['slug']], $method);
        }
    }
}
