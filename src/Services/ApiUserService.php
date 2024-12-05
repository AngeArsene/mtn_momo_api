<?php

namespace MtnMomoPaymentGateway\Services;

use Ramsey\Uuid\Uuid;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use MtnMomoPaymentGateway\Utils\Helper;

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
        $callback_url = Helper::env()->callback_url;

        $headers = [
            'X-Reference-Id' => $this->user_reference_id(),
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache',
            'Ocp-Apim-Subscription-Key' => Helper::env()->primary_key
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
    public function create_api_key(): string
    {
        if ($this->create_api_user()) {
            $headers = [
                'Cache-Control' => 'no-cache',
                'Ocp-Apim-Subscription-Key' => Helper::env()->primary_key
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
}
