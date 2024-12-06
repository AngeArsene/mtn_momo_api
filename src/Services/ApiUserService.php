<?php

namespace MtnMomoPaymentGateway\Services;

use Ramsey\Uuid\Uuid;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use MtnMomoPaymentGateway\Utils\Helper;
use MtnMomoPaymentGateway\Core\Application;

/**
 * Class ApiUserService
 * 
 * Represents the service class for managing API users and access tokens.
 */
final class ApiUserService 
{
    private Client $http_client;

    private const BASE_URL = 'https://sandbox.momodeveloper.mtn.com';

    /**
     * Constructor method to initialize the API service class with an HTTP client
     * 
     * @param Client $client The HTTP client to be used for API calls
     */
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

        $request = new Request('POST', self::BASE_URL.'/v1_0/apiuser', $headers, $body);

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

            $request = new Request('POST', self::BASE_URL.'/v1_0/apiuser/'.$this->user_reference_id().'/apikey', $headers);

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

    /**
     * Retrieve API user information and set the environment variable
     * 
     * @return string The target environment retrieved from the API
     */
    public function get_api_user_info(): string
    {
        if (!Helper::is_env_key_set('environment')) {
            $headers = [
                'Cache-Control' => 'no-cache',
                'Ocp-Apim-Subscription-Key' => Application::$PRIMARY_KEY
            ];

            $request = new Request('GET', self::BASE_URL.'/v1_0/apiuser/'.$this->user_reference_id(), $headers);

            $res = $this->http_client->send($request);
            $environment = json_decode($res->getBody())->targetEnvironment;

            return Helper::write_to_env('environment', $environment);
        }

        return Helper::env()->environment;
    }

    /**
     * Creates an access token for API authentication
     * 
     * @return string The generated access token
     */
    public function create_access_token(): string
    {
        Helper::remove_env_key('access_token');

        $username = $this->user_reference_id();
        $password = $this->create_api_key();
        $credentials = base64_encode("$username:$password");

        $headers = [
            'Cache-Control' => 'no-cache',
            'Ocp-Apim-Subscription-Key' => Application::$PRIMARY_KEY,
            'Authorization' => 'Basic ' . $credentials
        ];

        $request = new Request('POST', self::BASE_URL.'/collection/token/', $headers);

        $res = $this->http_client->send($request);

        if ($res->getStatusCode() == 200) {
            $access_token = json_decode($res->getBody())->access_token;
            return Helper::write_to_env('access_token', $access_token);
        }
    }

     /**
     * Get the status of a transaction
     * 
     * @return object|bool The status, payer number, amount, and transaction ID of the transaction
     */
    public function get_transaction_status(): object | bool
    {
        if (Helper::is_env_key_set('last_transaction_id')) {
            $transaction_id = Helper::env()->last_transaction_id;

            $headers = [
                        'X-Target-Environment' => $this->get_api_user_info(),
                        'Cache-Control' => 'no-cache',
                        'Ocp-Apim-Subscription-Key' => Application::$PRIMARY_KEY,
                        'Authorization' => 'Bearer ' . $this->create_access_token()
            ];

            $request = new Request('GET', self::BASE_URL.'/collection/v1_0/requesttopay/'.$transaction_id, $headers);

            $res = $this->http_client->send($request);

            $_data =  json_decode($res->getBody());

            return (object) [
                'status' => $_data->status,
                'payer_number' => $_data->payer->partyId,
                'amount' => $_data->amount,
                'transaction_id' => $_data->externalId
            ];
        }

        return false;
    }
}