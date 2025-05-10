<?php

namespace Infisical\Admin;



use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\Command\Guzzle\Description;
use GuzzleHttp\Command\Guzzle\Serializer;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use Infisical\Admin\TokenStorages\RuntimeTokenStorage;
use Infisical\Admin\Middleware\RefreshToken;
use Infisical\Admin\Serialization\FullBodyLocation;
use Infisical\Admin\Serialization\FullTextLocation;

/**
 * class InfisicalClient
 * @package Infisical\Admin
 * @method array getToken array $args=array() Get the access token;
 * @method array listFolders array $args=array() List all folders in the workspace directory;
 * @method array getFolderById array $args=array() Get a folder by id;
 * @method array createFolder array $args=array() { @command Infisical createFolder };
 * @method array updateFolder array $args=array() { @command Infisical updateFolder };
 * @method array deleteFolder array $args=array() { @command Infisical deleteFolder };
 * @method array listSecrets array $args=array() { @command Infisical listSecrets };
 * @method array createSecret array $args=array() { @command Infisical createSecret };
 * @method array retrieveSecret array $args=array() { @command Infisical retrieveSecret };
 * @method array updateSecret array $args=array() { @command Infisical updateSecret };
 * @method array deleteSecret array $args=array() { @command Infisical deleteSecret };
 * @method array bulkCreateSecrets array $args=array() { @command Infisical bulkCreateSecrets };
 * @method array bulkUpdateSecrets array $args=array() { @command Infisical bulkUpdateSecrets };
 * @method array bulkDeleteSecrets array $args=array() { @command Infisical bulkDeleteSecrets };
 * @method array attachTags array $args=array() { @command Infisical attachTags };
 * @method array detachTags array $args=array() { @command Infisical detachTags };
 */
class InfisicalClient extends GuzzleClient
{
    // Static factory method that receives Client and Description
    public static function factory(array $config)
    {
        $default = array(
            'apiVersion'  => '0.0.1',
            'clientId' => null,
            'clientSecret' => null,
            'baseUri'  => null,
            'token_storage' => new RuntimeTokenStorage(),
        );

        // Create client configuration
        $config = self::parseConfig($config, $default);

        $stack = new HandlerStack();
        $stack->setHandler(new CurlHandler());

        $middlewares = isset($config["middlewares"]) && is_array($config["middlewares"]) ? $config["middlewares"] : [];
        foreach ($middlewares as $middleware) {
            if (is_callable($middleware)) {
                $stack->push($middleware);
            }
        }

        $stack->push(new RefreshToken($config['token_storage']));

        $config['handler'] = $stack;

        $file = "{$config['apiVersion']}.php";
        $serviceDescriptionData = include __DIR__ . "/Resources/{$file}";
        $customOperations = isset($config["custom_operations"]) && is_array($config["custom_operations"]) ? $config["custom_operations"] : [];

        foreach ($customOperations as $operationKey => $operation) {
            // Do not override built-in functionality
            if (isset($serviceDescription['operations'][$operationKey])) {
                continue;
            }
            $serviceDescription['operations'][$operationKey] = $operation;
        }

        $description = new Description($serviceDescriptionData);

        return new static(
            new Client($config),
            $description,
            new Serializer($description, [
                "fullBody" => new FullBodyLocation(),
                "fullText" => new FullTextLocation(),
            ]),
            function ($response) {
                $responseBody = $response->getBody()->getContents();
                return json_decode($responseBody, true) ?? ['content' => $responseBody];
            },
            null,
            $config
        );
    }

    /**
     * Sets the BaseUri used by the Infisical Client
     *
     * @param string $baseUri
     */
    public function setBaseUri($baseUri)
    {
        $this->setConfig('baseUri', $baseUri);
    }


    /**
     * Gets the BaseUri used by the Infisical Client.
     *
     * @return string|null The configured base URI.
     */
    public function getBaseUri()
    {
        return $this->getConfig('baseUri');
    }

    /**
     * Attempt to parse config and apply defaults
     *
     * @param  array  $config
     * @param  array  $default
     *
     * @return array Returns the updated config array
     */
    protected static function parseConfig($config, $default)
    {
        array_walk($default, function ($value, $key) use (&$config) {
            if (!isset($config[$key])) {
                $config[$key] = $value;
            }
        });
        return $config;
    }
}
