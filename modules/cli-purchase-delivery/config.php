<?php

return [
    '__name' => 'cli-purchase-delivery',
    '__version' => '0.0.1',
    '__git' => 'git@github.com:getmim/cli-purchase-delivery.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'http://iqbalfn.com/'
    ],
    '__files' => [
        'modules/cli-purchase-delivery' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'purchase' => NULL
            ],
            [
                'lib-courier' => NULL
            ],
            [
                'lib-worker' => NULL
            ]
        ],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'CliPurchaseDelivery\\Controller' => [
                'type' => 'file',
                'base' => 'modules/cli-purchase-delivery/controller'
            ]
        ],
        'files' => []
    ],
    'gates' => [
        'cli-purchase' => [
            'priority' => 4000,
            'host' => [
                'value' => 'CLI'
            ],
            'path' => [
                'value' => 'purchase'
            ]
        ]
    ],
    'routes' => [
        'cli-purchase' => [
            404 => [
                'handler' => 'Cli\\Controller::show404'
            ],
            500 => [
                'handler' => 'Cli\\Controller::show500'
            ],
            'cliPurchaseDeliveryCheck' => [
                'info' => 'Check status of purchase delivery',
                'path' => [
                    'value' => 'delivery (:id)',
                    'params' => [
                        'id' => 'number'
                    ]
                ],
                'handler' => 'CliPurchaseDelivery\\Controller\\Delivery::check'
            ]
        ]
    ]
];
