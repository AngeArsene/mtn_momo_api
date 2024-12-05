<?php 

namespace MtnMomoPaymentGateway\Core;

use Ramsey\Uuid\Uuid;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use MtnMomoPaymentGateway\Utils\Helper;
use MtnMomoPaymentGateway\Services\ApiUserService;

/**
 * Class Application
 * 
 * Represents the main application class for handling MtnMomo payments.
 */
class Application 
{
    /** @var string The home directory path */
    public static string $HOME_DIR;

    private ApiUserService $service;
    
    /** @var Client HTTP client */
    private Client $http_client;
    
    /** @var string The primary key */
    public static string $PRIMARY_KEY;

    /** @var string The secondary key */
    public static string $SECONDARY_KEY;
    
    /** @var string The callback URL */
    public static string $CALLBACK_URL;

    private const CURRENCY = 'EUR';
    private const PAYER_NOTE = 'note';
    private const PAYER_MESSAGE = 'message';

    /**
     * Constructor method to initialize the Application class
     */
    public function __construct() 
    {
        self::$HOME_DIR = dirname(dirname(__DIR__));

        $this->http_client = new Client(['verify' => false]);
        
        $this->bootstrap();
    }

    /**
     * Initializes the application by setting primary and secondary keys
     */
    private function bootstrap(): void
    {
        self::$PRIMARY_KEY   = Helper::env()->primary_key;
        self::$SECONDARY_KEY = Helper::env()->secondary_key;
        self::$CALLBACK_URL   = Helper::env()->callback_url;

        // Initialize the API service
        $this->service = new ApiUserService($this->http_client);
    }

    /**
     * Make a request to pay a specified amount to the provided customer number
     * 
     * @param string $amount The amount to be paid
     * @param string $customer_number The customer number to make the payment to
     * @return int The HTTP status code of the request
     */
    public function request_to_pay(string $amount, string $customer_number): int
    {
        $headers = [
            'X-Reference-Id' => Uuid::uuid4()->toString(),
            'X-Target-Environment' => $this->service->get_api_user_info(),
            'Cache-Control' => 'no-cache',
            'Ocp-Apim-Subscription-Key' => Application::$PRIMARY_KEY,
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->service->create_access_token()
        ];

        $body = '{
            "amount": "'. $amount .'",
            "currency": "'. self::CURRENCY .'",
            "externalId": "33445",
            "payer": {
                "partyIdType": "MSISDN",
                "partyId": "'. $customer_number .'"
            },
            "payerMessage": "'. self::PAYER_MESSAGE .'",
            "payeeNote": "'. self::PAYER_NOTE .'"
        }';

        $request = new Request('POST', 'https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay', $headers, $body);

        $res = $this->http_client->send($request);
        return $res->getStatusCode();
    }
}