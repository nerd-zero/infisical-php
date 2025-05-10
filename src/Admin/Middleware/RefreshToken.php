<?php

namespace Infisical\Admin\Middleware;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\RejectedPromise;
use GuzzleHttp\Promise\RejectionException;
use Infisical\Admin\TokenStorages\TokenStorage;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RefreshToken
{
    /**
     * The token storage used to retrieve and save token credentials.
     *
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * Create a new RefreshToken instance.
     *
     * @param TokenStorage $tokenStorage
     */
    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Middleware handler to inject refreshed token into requests.
     *
     * @param callable $handler
     * @return callable
     */
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $token = $this->tokenStorage->getToken();

            return $this->refreshTokenIfNeeded($token, $options)
                ->then(function (array $cred) use ($request, $handler, $options) {
                    $this->tokenStorage->saveToken($cred);
                    $request = $request->withHeader('Authorization', 'Bearer ' . $cred['accessToken']);
                    return $handler($request, $options);
                })
                ->then(function (ResponseInterface $response) {
                    if ($response->getStatusCode() >= 400) {
                        $this->tokenStorage->saveToken([]);
                    }

                    return $response;
                })
                ->otherwise(function ($reason) {
                    $this->tokenStorage->saveToken([]);
                    throw $reason;
                });
        };
    }

    /**
     * Determine whether a new token needs to be retrieved or refreshed.
     *
     * @param array|null $credentials
     * @param array $options
     * @return PromiseInterface
     */
    protected function refreshTokenIfNeeded($credentials, $options)
    {
        if (!is_array($credentials) || empty($credentials['accessToken']) || empty($credentials['refresh_token'])) {
            return $this->getAccessToken($credentials, false, $options);
        }

        if (!$this->tokenExpired($credentials['accessToken'])) {
            return new FulfilledPromise($credentials);
        }

        if ($this->tokenExpired($credentials['refresh_token'])) {
            return $this->getAccessToken($credentials, false, $options);
        }

        return $this->getAccessToken($credentials, true, $options);
    }

    /**
     * Decode a JWT and return the payload.
     *
     * @param string $token
     * @return array
     */
    public function getTokenPayload($token)
    {
        if (!is_string($token)) {
            return [];
        }

        $token_parts = explode('.', $token);
        if (!isset($token_parts[1])) {
            return [];
        }
        $payload_encoded = $token_parts[1];
        $payload_decoded = base64_decode($payload_encoded);

        return json_decode($payload_decoded, true);
    }

    /**
     * Check if a given token is expired.
     *
     * @param string $token
     * @return bool
     */
    public function tokenExpired($token)
    {
        $info = $this->getTokenPayload($token);
        $exp = $info['exp'] ?? 0;
        return time() >= $exp;
    }

    /**
     * Request a new access token or refresh an existing one.
     *
     * @param array|null $credentials
     * @param bool $refresh
     * @param array $options
     * @return PromiseInterface
     */
    public function getAccessToken($credentials, $refresh, $options)
    {
        if ($refresh && empty($credentials['refresh_token'])) {
            return new RejectedPromise("cannot refresh token when the 'refresh_token' is missing");
        }



        $url = '/api/v1/auth/universal-auth/login';

        $clientId = $options["clientId"] ?? "admin-cli";

        $params = [
            'clientId' => $clientId,
            'clientSecret' => $options['clientSecret'],
        ];


        $httpClient = new Client([
            'base_uri' => $options['baseUri'],
            'verify' => isset($options['verify']) ? $options['verify'] : true,
        ]);


        return $httpClient->requestAsync('POST', $url, ['form_params' => $params])
            ->then(function (ResponseInterface $response) {
                if ($response->getStatusCode() !== 200) {
                    throw new RejectionException('expected to receive http status code 200 when requesting a token');
                }

                $serializedToken = $response->getBody()->getContents();
                $token = json_decode($serializedToken, true);

                if (!$token) {
                    throw new RejectionException('token returned in the response body is not in a valid json');
                }
                return $token;
            });
    }
}
