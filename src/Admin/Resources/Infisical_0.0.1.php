<?php
return array(
    'baseUri' => $config['baseUri'],
    'operations' => [
        'getToken' => [
            'httpMethod' => 'POST',
            'uri' => '/api/v1/auth/universal-auth/login',
            'responseModel' => 'getResponse',
            'parameters' => [
                'clientId' => [
                    'type' => 'string',
                    'location' => 'formParam'
                ],
                'clientSecret' => [
                    'type' => 'string',
                    'location' => 'formParam'
                ]
            ]
        ]
    ],
    'models' => [
        'getResponse' => [
            'type' => 'object',
            'additionalProperties' => [
                'location' => 'json'
            ]
        ]
    ]
);
