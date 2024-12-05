<?php 

namespace MtnMomoPaymentGateway\Core;

use Dotenv\Dotenv;
use Ramsey\Uuid\Uuid;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use MtnMomoPaymentGateway\Services\ApiUserService;
use MtnMomoPaymentGateway\Utils\Helper;

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

        var_dump($this->service->create_api_key());
    }

    /**
     * Initializes the application by setting primary and secondary keys
     */
    private function bootstrap(): void
    {
        self::$PRIMARY_KEY   = Helper::env()->primary_key;
        self::$SECONDARY_KEY = Helper::env()->secondary_key;
        self::$CALLBACK_URL   = Helper::env()->callback_url;

        $this->service = new ApiUserService($this->http_client);
    }
}