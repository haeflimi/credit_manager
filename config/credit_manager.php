<?php

return [
    'pacment_methods' => [
        'paypal' => [
            'environment' => 'sandbox',
            'client_id' => '',
            'sandbox_client_id' => '',
        ],
        'bank' => [
            'iban' => '',
            'name' => '',
            'pc' => '',
            'account_name' => ''
        ],
        'braintree' => [
            'environment'=>'',
            'merchant_id' => '',
            'public_key' => '',
            'private_key' => ''
        ],
    ],
    'negative_balance_limit' => 100,
    'currency_symbol' => '<i class="fa fa-dollar"></i>',
];
