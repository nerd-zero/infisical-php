<?php

require 'Definitions/v0.0.1.php';

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
                ...$defaultsQuery,
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
        'getFolderById' => [
            'httpMethod' => 'GET',
            'uri' => '/api/v1/folders/{id}',
            'responseModel' => 'getResponse',
            'parameters' => [
                ...$defaultsQuery,
                'id' => [
                    'type' => 'string',
                    'location' => 'uri',
                    'required' => true
                ],
            ]
        ],
        'createFolder' => [
            'httpMethod' => 'POST',
            'uri' => '/api/v1/folders',
            'responseModel' => 'getResponse',
            'parameters' => [
                ...$defaultsJson,
                'name' => [
                    'type' => 'string',
                    'location' => 'json',
                    'required' => true
                ],
                'directory' => [
                    'type' => 'string',
                    'location' => 'json'
                ],
                'description' => [
                    'type' => 'string',
                    'location' => 'json'
                ],
            ]
        ],
        'updateFolder' => [
            'httpMethod' => 'PATCH',
            'uri' => '/api/v1/folders/{folderId}',
            'responseModel' => 'getResponse',
            'parameters' => [
                ...$defaultsJson,
                'folderId' => [
                    'type' => 'string',
                    'location' => 'uri',
                    'description' => 'The ID of the folder to update.',
                    'required' => true
                ],
                'name' => [
                    'type' => 'string',
                    'location' => 'json',
                    'description' => 'The new name of the folder.',
                    'required' => true
                ],
                'directory' => [
                    'type' => 'string',
                    'location' => 'json'
                ],
                'description' => [
                    'type' => 'string',
                    'location' => 'json'
                ],
            ]
        ],
        'deleteFolder' => [
            'httpMethod' => 'DELETE',
            'uri' => '/api/v1/folders/{folderIdOrName}',
            'responseModel' => 'getResponse',
            'parameters' => [
                ...$defaultsJson,
                'folderIdOrName' => [
                    'type' => 'string',
                    'location' => 'uri',
                    'required' => true
                ],
            ]
        ],
        'listSecrets' => [
            'httpMethod' => 'GET',
            'uri' => '/api/v3/secrets/raw',
            'responseModel' => 'getResponse',
            'parameters' => [
                ...$defaultsQuery,
                'metadataFilter' => [
                    'type' => 'string',
                    'description' => 'The secret metadata key-value pairs to filter secrets by. When querying for multiple metadata pairs, the query is treated as an AND operation. Secret metadata format is key=value1,value=value2|key=value3,value=value4.',
                    'location' => 'query'
                ],
                'workspaceSlug' => [
                    'type' => 'boolean',
                    'description' => 'The slug of the project to list secrets from. This parameter is only applicable by machine identities.',
                    'location' => 'query'
                ],
                'secretPath' => [
                    'type' => 'string',
                    'description' => 'The secret path to list secrets from.',
                    'location' => 'query'
                ],
                'viewSecretValue' => [
                    'type' => 'string',
                    'description' => 'Whether or not to retrieve the secret value.',
                    'location' => 'query'
                ],
                'expandSecretReferences' => [
                    'type' => 'string',
                    'description' => 'Whether or not to expand secret references.',
                    'location' => 'query'
                ],
                'include_imports' => [
                    'type' => 'string',
                    'description' => 'Weather to include imported secrets or not.',
                    'location' => 'query'
                ],
                'recursive' => [
                    'type' => 'boolean',
                    'location' => 'query'
                ],
                'tagSlugs' => [
                    'type' => 'string',
                    'description' => 'The comma separated tag slugs to filter secrets.',
                    'location' => 'query'
                ],
                'tagIds' => [
                    'type' => 'string',
                    'description' => 'The comma separated tag ids to filter secrets.',
                    'location' => 'query'
                ],
            ]
        ],
        'createSecret' => [
            'httpMethod' => 'POST',
            'uri' => '/api/v3/secrets/raw{secretName}',
            'responseModel' => 'getResponse',
            'parameters' => [
                ...$defaultsJson,
                'secretName' => [
                    'type' => 'string',
                    'location' => 'uri',
                    'required' => true
                ],
                'secretValue' => [
                    'type' => 'string',
                    'location' => 'json',
                    'required' => true
                ],
                'secretComment' => [
                    'type' => 'string',
                    'description' => 'Attach a comment to the secret.',
                    'location' => 'json'
                ],
                'secretMetaata' => [
                    'type' => 'array',
                    'location' => 'json'
                ],
                'tagIds' => [
                    'type' => 'array',
                    'location' => 'json'
                ],
                'skipMultilineEncoding' => [
                    'type' => 'boolean',
                    'location' => 'json'
                ],
                'type' => [
                    'type' => 'string',
                    'description' => 'The type of the secret to create. ',
                    'location' => 'json'
                ],
                'secretPath' => [
                    'type' => 'string',
                    'description' => 'The secret path to create the secret in.',
                    'location' => 'json'
                ],
                'secretReminderRepeatDays' => [
                    'type' => 'integer',
                    'description' => 'Interval for secret rotation notifications, measured in days.',
                    'location' => 'json'
                ],
                'setReminderNote' => [
                    'type' => 'string',
                    'description' => 'Note to be attached in notification email.',
                    'location' => 'json'
                ],
            ]
        ],
        'retrieveSecret' => [
            'httpMethod' => 'GET',
            'uri' => '/api/v3/secrets/raw/{secretName}',
            'responseModel' => 'getResponse',
            'parameters' => [
                ...$defaultsQuery,
                'secretName' => [
                    'type' => 'string',
                    'location' => 'uri',
                    'required' => true
                ],
                'workspaceSlug' => [
                    'type' => 'string',
                    'description' => 'The slug of the project to get the secret from.',
                    'location' => 'query'
                ],
                'secretPath' => [
                    'type' => 'string',
                    'description' => 'The path of the secret to get.',
                    'location' => 'query'
                ],
                'viewSecretValue' => [
                    'type' => 'string',
                    'description' => 'Whether or not to retrieve the secret value',
                    'location' => 'query'
                ],
                'expandSecretReferences' => [
                    'type' => 'string',
                    'description' => 'Whether or not to expand secret references.',
                    'location' => 'query'
                ],
                'include_imports' => [
                    'type' => 'string',
                    'description' => 'Weather to include imported secrets or not.',
                    'location' => 'query'
                ],
            ]
        ],
        'updateSecret' => [
            'httpMethod' => 'PATCH',
            'uri' => '/api/v3/secrets/raw/{secretName}',
            'responseModel' => 'getResponse',
            'parameters' => [
                ...$defaultsJson,
                'secretName' => [
                    'type' => 'string',
                    'location' => 'uri',
                    'required' => true
                ],
                'secretValue' => [
                    'type' => 'string',
                    'description' => 'The new value of the secret.',
                    'location' => 'json'
                ],
                'secretPath' => [
                    'type' => 'string',
                    'description' => 'The default path for secrets to update or upsert, if not provided in the secret details.',
                    'location' => 'json'
                ],
                'skipMultilineEncoding' => [
                    'type' => 'boolean',
                    'location' => 'json'
                ],
                'type' => [
                    'type' => 'string',
                    'description' => 'The type of the secret to update.',
                    'location' => 'json'
                ],
                'tagIds' => [
                    'type' => 'array',
                    'location' => 'json'
                ],
                'metadata' => [
                    'type' => 'object',
                    'location' => 'json'
                ],
                'secretMetadata' => [
                    'type' => 'array',
                    'location' => 'json'
                ],
                'secretReminderRepeatDays' => [
                    'type' => 'integer',
                    'description' => 'Interval for secret rotation notifications, measured in days.',
                    'location' => 'json'
                ],
                'setReminderNote' => [
                    'type' => 'string',
                    'description' => 'Note to be attached in notification email.',
                    'location' => 'json'
                ],
                'secretReminderRecipients' => [
                    'type' => 'array',
                    'description' => 'An array of user IDs that will receive the reminder email. If not specified, all project members will receive the reminder email.',
                    'location' => 'json'
                ],
                'newSecretName' => [
                    'type' => 'string',
                    'description' => 'The new name for the secret.',
                    'location' => 'json'
                ],
                'secretComment' => [
                    'type' => 'string',
                    'description' => 'Update comment to the secret.',
                    'location' => 'json'
                ],
            ],
        ],
        'deleteSecret' => [
            'httpMethod' => 'DELETE',
            'uri' => '/api/v3/secrets/raw/{secretName}',
            'responseModel' => 'getResponse',
            'parameters' => [
                ...$defaultsJson,
                'secretName' => [
                    'type' => 'string',
                    'location' => 'uri',
                    'required' => true
                ],
                'secretPath' => [
                    'type' => 'string',
                    'description' => 'The path of the secret to delete.',
                    'location' => 'json'
                ],
                'type' => [
                    'type' => 'string',
                    'description' => 'The type of the secret to delete.',
                    'location' => 'json'
                ],
            ]
        ],
        'bulkCreateSecrets' => [
            'httpMethod' => 'POST',
            'uri' => '/api/v3/secrets/batch/raw',
            'responseModel' => 'getResponse',
            'parameters' => [
                ...$defaultsJson,
                'secrets' => [
                    'type' => 'array',
                    'location' => 'json',
                    'required' => true
                ],
                'projectSlug' => [
                    'type' => 'string',
                    'description' => 'The slug of the project to update the secret in.',
                    'location' => 'json'
                ],
                'secretPath' => [
                    'type' => 'string',
                    'description' => 'The path of the secret to create.',
                    'location' => 'json'
                ]
            ],
        ],
        'bulkUpdateSecrets' => [
            'httpMethod' => 'PATCH',
            'uri' => '/api/v3/secrets/batch/raw',
            'responseModel' => 'getResponse',
            'parameters' => [
                ...$defaultsJson,
                'secrets' => [
                    'type' => 'array',
                    'location' => 'json',
                    'required' => true
                ],
                'projectSlug' => [
                    'type' => 'string',
                    'description' => 'The slug of the project to update the secret in.',
                    'location' => 'json'
                ],
                'secretPath' => [
                    'type' => 'string',
                    'description' => 'The path of the secret to update.',
                    'location' => 'json'
                ],
                'mode' => [
                    'type' => 'string',
                    'description' => 'Defines how the system should handle missing secrets during an update.',
                    'location' => 'json'
                ],
            ],
        ],
        'bulkDeleteSecrets' => [
            'httpMethod' => 'DELETE',
            'uri' => '/api/v3/secrets/batch/raw',
            'responseModel' => 'getResponse',
            'parameters' => [
                ...$defaultsJson,
                'secrets' => [
                    'type' => 'array',
                    'location' => 'json',
                    'required' => true
                ],
                'projectSlug' => [
                    'type' => 'string',
                    'description' => 'The slug of the project to update the secret in.',
                    'location' => 'json'
                ],
                'secretPath' => [
                    'type' => 'string',
                    'description' => 'The path of the secret to delete.',
                    'location' => 'json'
                ],
            ],
        ],
        'attachTags' => [
            'httpMethod' => 'POST',
            'uri' => '/api/v3/secrets/tags/{secretName}',
            'responseModel' => 'getResponse',
            'parameters' => [
                ...$defaultsJson,
                'secretName' => [
                    'type' => 'string',
                    'location' => 'uri',
                    'required' => true
                ],
                'projectSlug' => [
                    'type' => 'string',
                    'description' => 'The slug of the project where the secret is located.',
                    'location' => 'json',
                    'required' => true
                ],
                'tagSlugs' => [
                    'type' => 'array',
                    'location' => 'json',
                    'description' => 'An array of existing tag slugs to attach to the secret.',
                    'required' => true
                ],
                'secretPath' => [
                    'type' => 'string',
                    'location' => 'json',
                    'description' => 'The path of the secret to attach tags to.',
                ],
                'type' => [
                    'type' => 'string',
                    'description' => 'The type of the secret to attach tags to. (shared/personal)',
                    'location' => 'json'
                ],
            ],
        ],
        'detachTags' => [
            'httpMethod' => 'DELETE',
            'uri' => '/api/v3/secrets/tags/{secretName}',
            'responseModel' => 'getResponse',
            'parameters' => [
                ...$defaultsJson,
                'secretName' => [
                    'type' => 'string',
                    'location' => 'uri',
                    'required' => true
                ],
                'projectSlug' => [
                    'type' => 'string',
                    'description' => 'The slug of the project where the secret is located.',
                    'location' => 'json',
                    'required' => true
                ],
                'tagSlugs' => [
                    'type' => 'array',
                    'location' => 'json',
                    'description' => 'An array of existing tag slugs to detach from the secret.',
                    'required' => true
                ],
                'secretPath' => [
                    'type' => 'string',
                    'location' => 'json',
                    'description' => 'The path of the secret to detach tags from.',
                ],
                'type' => [
                    'type' => 'string',
                    'description' => 'The type of the secret to detach tags from. (shared/personal)',
                    'location' => 'json'
                ],
            ],
        ],
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
