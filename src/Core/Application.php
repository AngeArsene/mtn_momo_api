<?php 

namespace MtnMomoPaymentGateway\Core;

use GuzzleHttp\Client;
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
    
    /** @var string The secondary key */
    public static string $CALLBACK_URL;

    /**
     * Constructor method
     */
    public function __construct() 
    {
        self::$HOME_DIR = dirname(dirname(__DIR__));

        $this->http_client = new Client(['verify' => false]);
        
        $this->bootstrap();

        var_dump($this->service->create_access_token());
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
}