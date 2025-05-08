<?php

namespace InfisicalPhp\App\Admin;

require_once __DIR__ . '/../../vendor/autoload.php';


use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\Command\Guzzle\Description;

/**
 * class InfisicalClient
 * @package Infisical\Admin\Client
 * @method array getToken array $args=array() Get the access token
 */
class InfisicalClient extends GuzzleClient
{

    public function __construct(Client $client, Description $description)
    {
        parent::__construct($client, $description);
    }

    // Static factory method that receives Client and Description
    public static function factory(array $config)
    {
        $client = new Client();
        $file = 'Infisical_0.0.1.php';
        $serviceDescriptionData = include __DIR__ . "/Resources/{$file}";
        $description = new Description($serviceDescriptionData);
        return new self($client, $description);
    }

    /**
     * Sets the BaseUri used by the Keycloak Client
     *
     * @param string $baseUri
     */
    public function setBaseUri($baseUri)
    {
        $this->setConfig('baseUri', $baseUri);
    }


    /**
     * Sets the Realm name used by the Keycloak Client
     */
    public function getBaseUri()
    {
        return $this->getConfig('baseUri');
    }
}
