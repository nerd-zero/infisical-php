<?php
$defaults = [
    'workspaceId' => [
        'type' => 'string',
        'description' => 'The ID of the project to list folders from.',
        'location' => 'query',
        'required' => true
    ],
    'environment' => [
        'type' => 'string',
        'location' => 'query'
    ],
    'path' => [
        'type' => 'string',
        'location' => 'query'
    ]
];
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
        ],
        'listFolders' => [
            'httpMethod' => 'GET',
            'uri' => '/api/v1/folders',
            'responseModel' => 'getResponse',
            'parameters' => [
                ...$defaults,
                'directory' => [
                    'type' => 'string',
                    'location' => 'query'
                ],
                'recursive' => [
                    'type' => 'boolean',
                    'location' => 'query'
                ],
            ]
        ],
        'createFolder' => [
            'httpMethod' => 'POST',
            'uri' => '/api/v1/folders',
            // 'responseModel' => 'getResponse',
            'parameters' => [
                ...$defaults,
                'name' => [
                    'type' => 'string',
                    'location' => 'json',
                    'required' => true
                ],
                'directory' => [
                    'type' => 'string',
                    'location' => 'query'
                ],
                'description' => [
                    'type' => 'string',
                    'location' => 'query'
                ],
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
