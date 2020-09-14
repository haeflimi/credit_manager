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
    // Define a set of Groups that is Usable as Filter in the Credit Manager Backend
    'relevant_groups' => [
        //'group-id' => 'Filter name in CM overview'
    ],
    // Define a Topc Attribute that is beeing used to store Credit Manager Categories for Transactions
    'categories_topic' => 0,
    'negative_balance_limit' => 100,
    'currency_symbol' => '<i class="fa fa-dollar"></i>',
];
