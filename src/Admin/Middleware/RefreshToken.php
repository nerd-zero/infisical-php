<?php // This line tells the computer that the following code is written in PHP, a programming language for web stuff.

namespace Infisical\Admin\Middleware; // This is like putting our code in a special folder called "InfisicalPhp\Admin\Middleware" so it's organized and doesn't get mixed up with other code.

// These lines are like saying, "We need to use some tools that other people have already made."
use GuzzleHttp\Client; // We're using a tool called "Client" from "GuzzleHttp" to help us talk to other computers over the internet.
use GuzzleHttp\Promise\FulfilledPromise; // This tool helps us say "I promise this task was successful!"
use GuzzleHttp\Promise\PromiseInterface; // This is like a contract for promises, defining what all promises should be able to do.
use GuzzleHttp\Promise\RejectedPromise; // This tool helps us say "I promise this task failed."
use GuzzleHttp\Promise\RejectionException; // This is a special kind of "oops" message when a promise fails.
use Infisical\Admin\TokenStorages\TokenStorage;
use Psr\Http\Message\RequestInterface; // This is like a blueprint for how to write a letter (request) to send to another computer.
use Psr\Http\Message\ResponseInterface; // This is like a blueprint for how to understand the reply (response) from another computer.

class RefreshToken // We're creating a blueprint for a "RefreshToken" helper. Its job is to make sure we always have a fresh key.
{
    /** // This is a special comment block explaining what's next.
     * @var TokenStorage // It says the variable below, "$tokenStorage", will hold a "TokenStorage" tool.
     */
    private $tokenStorage; // This is like a private box inside our "RefreshToken" helper where we keep our "TokenStorage" tool. Only "RefreshToken" can use it directly.

    public function __construct(TokenStorage $tokenStorage) // This is a special instruction that runs when we create a new "RefreshToken" helper.
    { // It needs a "TokenStorage" tool to be given to it.
        $this->tokenStorage = $tokenStorage; // We take the given "TokenStorage" tool and put it in our private box.
    }

    public function __invoke(callable $handler) // This makes our "RefreshToken" helper act like a function. When we "call" it, this code runs.
    { // It takes another function (called "$handler") as a helper.
        return function (RequestInterface $request, array $options) use ($handler) { // It gives back a new, unnamed function that will do the main work.
            // This new function will take a "letter" ($request) and some "options" (settings).
            // 'use ($handler)' means this inner function can also use the '$handler' from outside.

            $token = $this->tokenStorage->getToken(); // We ask our "TokenStorage" tool to get the current key we have saved.

            // We try to refresh the key if it's old, and then we do something.
            return $this->refreshTokenIfNeeded($token, $options) // Call a helper function to check and refresh the key. It returns a "promise".
                ->then(function (array $cred) use ($request, $handler, $options) { // If the key is ready (or we got a new one successfully - this is the "then" part of the promise)...
                    $this->tokenStorage->saveToken($cred); // We save the (new) key using our "TokenStorage" tool. "$cred" holds the key information.
                    $request = $request->withHeader('Authorization', 'Bearer ' . $cred['accessToken']); // We add the new key to our "letter" ($request) so the server knows who we are. 'Bearer' is like saying "The person carrying this key is authorized".
                    return $handler($request, $options); // Then, we use the original helper function ($handler) to send the "letter" with the key.
                })
                ->then(function (ResponseInterface $response) { // After the letter is sent and we get a reply ($response)...
                    if ($response->getStatusCode() >= 400) { // If the reply says there was a problem (like a "not allowed" error, which often have codes 400 or higher)...
                        $this->tokenStorage->saveToken([]); // We erase the key we have saved, because it might be bad. An empty list [] means no key.
                    }

                    return $response; // We give back the reply we got.
                })
                ->otherwise(function ($reason) { // If something went wrong while trying to get or refresh the key (this is the "otherwise" part of the promise)...
                    $this->tokenStorage->saveToken([]); // We erase the key we have saved, because something is wrong.
                    throw $reason; // We signal that a big error happened, using the "$reason" for the failure. This is like shouting "Problem!"
                });
        };
    }

    /** // This is a special comment block.
     * Check we need to refresh token and refresh if needed // It explains what this function does.
     *
     * @param ?array $credentials // It says this function takes "$credentials" which might be a list (array) of key details, or nothing (? means it can be null).
     * @param $options // It also takes some "$options" (settings).
     * @return PromiseInterface // It says this function will give back a "promise".
     */
    protected function refreshTokenIfNeeded($credentials, $options) // This function is like a helper that checks if we need a new key. "protected" means only this blueprint and its children can use it.
    {

        // If we don't have proper key details (like if "$credentials" isn't a list, or we're missing the main key 'access_token' or the 'refresh_token')...
        if (!is_array($credentials) || empty($credentials['accessToken']) || empty($credentials['refresh_token'])) {
            // '!is_array' means "is not a list". 'empty()' checks if it's missing or has no value.
            return $this->getAccessToken($credentials, false, $options); // Then we need to get a brand new key. "false" here means we're not trying to "refresh" an existing one with a refresh token.
        }

        // If our main key ('access_token') is not old (not expired)...
        if (!$this->tokenExpired($credentials['accessToken'])) { // '$this->tokenExpired(...)' calls another helper in this blueprint. '!' means "not".
            return new FulfilledPromise($credentials); // We don't need to do anything! We promise that the current key details ($credentials) are good. "FulfilledPromise" means "Yay, it worked!".
        }

        // If our main key IS old, but our "refresh key" (a special key to get a new main key) is ALSO old...
        if ($this->tokenExpired($credentials['refresh_token'])) {
            return $this->getAccessToken($credentials, false, $options); // Then we need to get a brand new key (not using the old, expired refresh key).
        }

        // If our main key is old, but our refresh key is still good...
        return $this->getAccessToken($credentials, true, $options); // Then we use the refresh key to get a new main key. "true" here means we ARE trying to "refresh".
    }

    /** // Special comment block.
     * Get Access Token data // Explains what this function does: it looks inside the key.
     *
     * @param string $token // It takes a "$token" (the key itself, which is text, like "abc.123.xyz").
     * @return array // It gives back a list (array) of information found inside the key.
     */
    public function getTokenPayload($token) // This function helps us read the information hidden inside a key. "public" means anyone can use it.
    {
        if (!is_string($token)) { // If the given "$token" is not text (a string)...
            return []; // We can't do anything, so we give back an empty list.
        }

        // Keys (JSON Web Tokens or JWTs) are often made of three parts separated by dots (.). We usually need the middle part.
        $token_parts = explode('.', $token); // This splits the key text into pieces wherever there's a dot. So "a.b.c" becomes ["a", "b", "c"].
        // The middle part (payload) is usually encoded in a special way (Base64) so it can be sent as text easily.
        // We need to make sure there is a second part before trying to access it.
        if (!isset($token_parts[1])) { // If there's no second part...
            return []; // ...it's not a valid token for us, so return an empty list.
        }
        $payload_encoded = $token_parts[1]; // The second piece (at index 1, because lists start at 0) is the encoded payload.
        $payload_decoded = base64_decode($payload_encoded); // We decode it from Base64. It's like using a secret decoder ring.

        // The decoded information is usually in a format called JSON (JavaScript Object Notation), which is text that looks like a list.
        return json_decode($payload_decoded, true); // We change this JSON text into a list that PHP can understand. "true" makes it a list of key-value pairs (an associative array).
    }

    /** // Special comment block.
     * Check token expiration // Explains what this function does: checks if a key is too old.
     *
     * @param string $token // It takes a "$token" (the key, which is text).
     * @return bool // It gives back "true" if the key is old, or "false" if it's still good. (bool means boolean: true or false).
     */
    public function tokenExpired($token) // This function checks if a key has passed its "use by" date.
    {
        $info = $this->getTokenPayload($token); // First, we get the information (payload) hidden inside the key.
        // We look for the "exp" (expiration time) in the info. This is usually a number representing seconds since a long time ago (Unix timestamp).
        // '?? 0' means if 'exp' is not found in $info, use 0 as a default.
        $exp = $info['exp'] ?? 0;
        if (time() < $exp) { // 'time()' gives the current time (also in seconds since that long time ago).
            // If the current time is BEFORE the expiration time...
            return false; // ...then the key is NOT expired, so we return "false".
        }
        return true; // Otherwise (if current time is at or after expiration time, or if exp was 0 or invalid), the key IS expired (or considered expired), so we return "true".
    }

    /** // Special comment block.
     * Refresh access token // Explains what this function does: it gets a new key from the server.
     *
     * @param array|null $credentials // It might take current key details ($credentials), which could be a list or nothing (null).
     * @param $refresh // It takes a "$refresh" flag (true/false) to know if we're using a refresh key.
     * @param $options // It takes some "$options" (settings for how to get the key).
     * @return PromiseInterface // It gives back a "promise" because getting a key from a server takes time and might succeed or fail.
     */
    public function getAccessToken($credentials, $refresh, $options) // This function does the actual work of asking the server for a new key.
    {
        // If we're trying to use a refresh key ($refresh is true) but we don't have one in our $credentials...
        if ($refresh && empty($credentials['refresh_token'])) {
            // 'empty()' checks if 'refresh_token' is missing or has no value.
            return new RejectedPromise("cannot refresh token when the 'refresh_token' is missing"); // We can't do it, so we return a "failed promise" with an error message.
        }


        // This is like the specific address (endpoint) on the server we need to send our request to get a token.
        // It uses a part of the address from the "$options" called 'realm'. {$options['realm']} puts the value of $options['realm'] into the string.
        $url = '/api/v1/auth/universal-auth/login';
        // This is like our app's name or ID for the server. If not provided in options, it defaults to "admin-cli".
        $clientId = $options["clientId"] ?? "admin-cli"; // '??' is the null coalescing operator: use $options["client_id"] if it exists and is not null, otherwise use "admin-cli".
        // This tells the server how we're trying to get the key (the "grant type").
        // If "$refresh" is true, we use "refresh_token" grant type.
        // Otherwise, we use what's in "$options['grant_type']" or default to "password" grant type.
        // We prepare a list of information ($params) to send to the server.
        $params = [ // This is an array (list) of parameters.
            'clientId' => $clientId, // We tell the server our client ID.
            'clientSecret' => $options['clientSecret'], // We tell the server how we want to get the key.
        ];

        // Sometimes, a client secret might be needed even with other grant types, or it might be optional.
        // if (!empty($options['clientSecret'])) { // If a 'client_secret' is provided in the options and it's not empty...
        //     $params['client_secret'] = $options['client_secret']; // We add it (or overwrite it if set by client_credentials grant type) to the parameters we send to the server.
        // }

        // We create a new "GuzzleHttp\Client" tool to help us send the request to the server.
        $httpClient = new Client([ // We're setting up our internet messenger (HTTP client).
            'base_uri' => $options['baseUri'], // This is the main part of the server's address (like "https://auth.example.com/auth/"). The $url above will be added to this.
            // 'verify' tells it whether to check if the server's security certificate (SSL/TLS) is valid.
            // 'isset($options['verify'])' checks if 'verify' exists in the $options array.
            // If 'verify' is set in options, use that value. Otherwise, default to 'true' (which means do verify the certificate).
            'verify' => isset($options['verify']) ? $options['verify'] : true,
        ]);

        // We tell our httpClient to send a "POST" request (a way of sending data, often used for login or submitting forms) to the $url.
        // We also send our $params data as 'form_params' (like filling out a web form).
        // 'requestAsync' means we send the request but don't wait here for the answer; it will happen in the background and return a "promise".
        return $httpClient->requestAsync('POST', $url, ['form_params' => $params])
            ->then(function (ResponseInterface $response) { // If the server replies successfully (this is the "then" part of the promise, it runs when the promise is fulfilled)...
                // The server should reply with HTTP status code 200, which means "OK" or "Success".
                if ($response->getStatusCode() !== 200) { // '$response->getStatusCode()' gets the status code from the reply. If it's not 200...
                    // We "throw" an error (RejectionException), meaning something went wrong with our request for a token.
                    throw new RejectionException('expected to receive http status code 200 when requesting a token');
                }

                $serializedToken = $response->getBody()->getContents(); // We get the main content (body) of the server's reply. This should be the new token information, usually as JSON text.
                $token = json_decode($serializedToken, true); // The token info is usually in JSON format, so we change it into a list (associative array) PHP can use. 'true' makes it an array.

                if (!$token) { // If we couldn't change the reply into a list (meaning it wasn't valid JSON, or it was empty)...
                    // We "throw" an error.
                    throw new RejectionException('token returned in the response body is not in a valid json');
                }
                return $token; // If everything was fine, we give back the new token information (which is now a PHP array).
            }); // If the requestAsync fails for network reasons, or if we throw a RejectionException, the promise will be rejected.
    }
}
