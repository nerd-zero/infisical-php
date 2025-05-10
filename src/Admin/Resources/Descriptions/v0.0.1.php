
<?php
$defaultsQuery = [
    'workspaceId' => [
        'type' => 'string',
        'description' => 'The ID of the project to list folders from.',
        'location' => 'query',
    ],
    'environment' => [
        'type' => 'string',
        'location' => 'query',
    ],
    'path' => [
        'type' => 'string',
        'location' => 'query'
    ]
];

$defaultsJson = [
    'workspaceId' => [
        'type' => 'string',
        'description' => 'The ID of the project to list folders from.',
        'location' => 'json',
    ],
    'environment' => [
        'type' => 'string',
        'location' => 'json',
        'required' => true,
    ],
    'path' => [
        'type' => 'string',
        'location' => 'json',
    ]
];
