<?php

namespace MtnMomoPaymentGateway\Services;

use Ramsey\Uuid\Uuid;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use MtnMomoPaymentGateway\Utils\Helper;
use MtnMomoPaymentGateway\Core\Application;

final class ApiUserService 
{
    private Client $http_client;

    public function __construct(Client $client)
    {
        $this->http_client = $client;
    }

    /**
     * Creates an API user
     *
     * @return bool Returns true if API user creation was successful; otherwise, false
     */
    private function create_api_user(): bool
    {
        $callback_url = Application::$CALLBACK_URL;

        $headers = [
            'X-Reference-Id' => $this->user_reference_id(),
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache',
            'Ocp-Apim-Subscription-Key' => Application::$PRIMARY_KEY
        ];

        $body = '{
            "providerCallbackHost": ' . '"'.$callback_url.'"' . '
        }';

        $request = new Request('POST', 'https://sandbox.momodeveloper.mtn.com/v1_0/apiuser', $headers, $body);

        try {
            $res = $this->http_client->send($request);
            return $res->getStatusCode() === 201 ?: $this->create_api_user();

        } catch (\Throwable $e) { return false; }
    }

    /**
     * Creates an API key
     *
     * @return string The generated API key
     */
    private function create_api_key(): string
    {
        if ($this->create_api_user()) {
            $headers = [
                'Cache-Control' => 'no-cache',
                'Ocp-Apim-Subscription-Key' => Application::$PRIMARY_KEY
            ];

            $request = new Request('POST', 'https://sandbox.momodeveloper.mtn.com/v1_0/apiuser/'.$this->user_reference_id().'/apikey', $headers);

            $res = $this->http_client->send($request);
            $api_key = json_decode($res->getBody())->apiKey;

            return Helper::write_to_env('user_api_key', $api_key);

        } else {
            return Helper::env()->user_api_key;
        }
    }

    /**
     * Generates a user reference ID
     *
     * @return string The generated user reference ID
     */
    protected function user_reference_id(): string
    {
        $uuid = Uuid::uuid4();

        return Helper::write_to_env('user_reference_id', $uuid);
    }

    public function get_api_user_info(): string
    {
        if (!Helper::is_env_key_set('environment')) {
            // Retrieve API user information using the generated user reference ID
            $headers = [
                'Cache-Control' => 'no-cache',
                'Ocp-Apim-Subscription-Key' => Application::$PRIMARY_KEY
            ];

            $request = new Request('GET', 'https://sandbox.momodeveloper.mtn.com/v1_0/apiuser/'.$this->user_reference_id(), $headers);

            $res = $this->http_client->send($request);
            $environment = json_decode($res->getBody())->targetEnvironment;

            return Helper::write_to_env('environment', $environment);
        }

        return Helper::env()->environment;
    }

    public function create_access_token()
    {
        // Basic authentication credentials
        $username = $this->user_reference_id();
        $password = $this->create_api_key();
        $credentials = base64_encode("$username:$password");

        $headers = [
            'Cache-Control' => 'no-cache',
            'Ocp-Apim-Subscription-Key' => Application::$PRIMARY_KEY,
            'Authorization' => 'Basic ' . $credentials
        ];

        $request = new Request('POST', 'https://sandbox.momodeveloper.mtn.com/collection/token/', $headers);

        $res = $this->http_client->send($request);

        if ($res->getStatusCode() == 200) {
            $access_token = json_decode($res->getBody())->access_token;
            return Helper::write_to_env('access_token', $access_token);
        }
    }
}
