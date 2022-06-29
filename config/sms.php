<?php

return [

    'default' => env('SMS_GATEWAY', 'farazsms'),

    'regex' => '/^09(1[0-9]|9[0-2,8]|2[0-2]|0[1-5]|41|3[0,3,5-9])\d{7}$/',

    'gateways' => [

        'melipayamak' => [
            'username' => env('MELIPAYAMAK_USERNAME'),
            'password' => env('MELIPAYAMAK_PASSWORD'),
            'line' => env('MELIPAYAMAK_LINE'),
            'verify_pattern_id' => env('MELIPAYAMAK_VERIFY_PATTERN_ID')
        ],

        'farazsms' => [
            'apikey' => env('FARAZSMS_APIKEY'),
            'sms_line' => env('FARAZSMS_LINE'),
            'pattern_line' => env('FARAZSMS_PATTERN_LINE'),
            'verify_pattern_id' => env('FARAZSMS_VERIFY_PATTERN_ID'),
            'pattern_fields' => [
                'name', 'code'
            ]
        ]

    ]

];
