{{REWRITTEN_CODE}}
<?php

namespace InfisicalPhp\App\Admin;

require_once __DIR__ . '/../../vendor/autoload.php';


use GuzzleHttp\Client;
use GuzzleHttp\Command\Guzzle\GuzzleClient;
use GuzzleHttp\Command\Guzzle\Description;

/**
 * class InfisicalClient
 * @package InfisicalPhp\App\Admin
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
        $default = array();
        $client = new Client();
        $file = 'Infisical_0.0.1.php';
        $serviceDescriptionData = include __DIR__ . "/Resources/{$file}";
        $description = new Description($serviceDescriptionData);
        return new self($client, $description);
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
