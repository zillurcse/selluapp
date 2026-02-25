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
                'name' => 'Digital Wallet',
                'slug' => 'wallet',
                'icon' => 'Wallet',
                'description' => 'Mobile wallet systems (bKash/Nagad).',
                'is_active' => true,
                'config_fields' => [
                    ['name' => 'walletType', 'label' => 'Wallet Type', 'type' => 'select', 'options' => ['bkash', 'nagad', 'rocket', 'upay']],
                    ['name' => 'accountType', 'label' => 'Account Type', 'type' => 'select', 'options' => ['Personal', 'Agent', 'Merchant']],
                    ['name' => 'number', 'label' => 'Mobile Number', 'type' => 'text', 'placeholder' => '01XXXXXXXXX'],
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
