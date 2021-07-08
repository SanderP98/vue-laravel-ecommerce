<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DateTime;
use DB;

class PaymentMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paymentMethods = [
            [
                'name' => "applepay",
                'is_active' => false,
                'image' => ''
            ],
            [
                'name' => "bancontact",
                'is_active' => false,
                'image' => ''
            ],
            [
                'name' => "banktransfer",
                'is_active' => false,
                'image' => ''
            ],
            [
                'name' => "creditcard",
                'is_active' => false,
                'image' => ''
            ],
            [
                'name' => "directdebit",
                'is_active' => false,
                'image' => ''
            ],
            [
                'name' => "eps",
                'is_active' => false,
                'image' => ''
            ],
            [
                'name' => "giftcard",
                'is_active' => false,
                'image' => ''
            ],
            [
                'name' => "giropay",
                'is_active' => false,
                'image' => ''
            ],
            [
                'name' => "ideal",
                'is_active' => false,
                'image' => ''
            ],
            [
                'name' => "kbc",
                'is_active' => false,
                'image' => ''
            ],
            [
                'name' => "mybank",
                'is_active' => false,
                'image' => ''
            ],
            [
                'name' => "paypal",
                'is_active' => false,
                'image' => ''
            ],
            [
                'name' => "paysafecard",
                'is_active' => false,
                'image' => ''
            ],
            [
                'name' => "przelewy24",
                'is_active' => false,
                'image' => ''
            ],
            [
                'name' => "sofort",
                'is_active' => false,
                'image' => ''
            ],
            [
                'name' => "belfius",
                'is_active' => false,
                'image' => ''
            ]
        ];

        DB::table('payment_methods')->insert($paymentMethods);
    }
}
