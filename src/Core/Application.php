<?php 

namespace MtnMomoPaymentGateway\Core;

use Dotenv\Dotenv;
use Ramsey\Uuid\Uuid;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * Class Application
 * 
 * Represents the main application class for handling MtnMomo payments.
 */
class Application 
{
    /** @var string The home directory path */
    public static string $HOME_DIR;

    /** @var Client HTTP client */
    private Client $http_client;
    
    /** @var string The primary key */
    protected static string $PRIMARY_KEY;

    /** @var string The secondary key */
    protected static string $SECONDARY_KEY;

    /**
     * Constructor method
     */
    public function __construct() 
    {
        self::$HOME_DIR = dirname(dirname(__DIR__));

        $this->bootstrap();
        
        $this->http_client = new Client(['verify' => false]);

        var_dump($this->create_api_key());
    }

    /**
     * Initializes the application by setting primary and secondary keys
     */
    private function bootstrap(): void
    {
        self::$PRIMARY_KEY   = $this->env()->primary_key;
        self::$SECONDARY_KEY = $this->env()->secondary_key;
    }

    /**
     * Loads environment variables
     *
     * @return object An object containing environment variables
     */
    private function env(): object
    {
        $dotenv = Dotenv::createImmutable(self::$HOME_DIR);
        $dotenv->load();

        return (object) $_ENV;
    }

    /**
     * Generates a user reference ID
     *
     * @return string The generated user reference ID
     */
    protected function user_reference_id(): string
    {
        $uuid = Uuid::uuid4();

        return $this->write_to_env('user_reference_id', $uuid);
    }

    /**
     * Creates an API key
     *
     * @return string The generated API key
     */
    protected function create_api_key(): string
    {
        if ($this->create_api_user()) {
            $headers = [
                'Cache-Control' => 'no-cache',
                'Ocp-Apim-Subscription-Key' => $this->env()->primary_key
            ];

            $request = new Request('POST', 'https://sandbox.momodeveloper.mtn.com/v1_0/apiuser/'.$this->user_reference_id().'/apikey', $headers);

            $res = $this->http_client->send($request);
            $api_key = json_decode($res->getBody())->apiKey;

            return $this->write_to_env('user_api_key', $api_key);

        } else {
            return $this->env()->user_api_key;
        }
    }

    /**
     * Creates an API user
     *
     * @return bool Returns true if API user creation was successful; otherwise, false
     */
    private function create_api_user(): bool
    {
        $callback_url = $this->env()->callback_url;

        $headers = [
            'X-Reference-Id' => $this->user_reference_id(),
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache',
            'Ocp-Apim-Subscription-Key' => $this->env()->primary_key
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
     * Writes a key-value pair to the .env file
     *
     * @param string $key The key to write
     * @param string $value The value to write
     * @return string The value that was written
     */
    private function write_to_env(string $key, string $value): string
    {
        $envFile = self::$HOME_DIR . DIRECTORY_SEPARATOR . '.env';

        // Read the existing content of the .env file
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        // Initialize variables for found value and flag
        $foundValue = null;
        $found = false;

        // Search for the key in the existing lines
        foreach ($lines as &$line) {
            if (strpos($line, $key . '=') === 0) {
                $foundValue = trim(explode('=', $line, 2)[1]); // Get the current value
                $found = true;
                break;
            }
        }

        // If the key was found and has a non-empty value, return it
        if ($found) {
            if ($foundValue !== '') {
                return $foundValue;
            } else {
                $lineIndex = array_search($line, $lines);
                $lines[$lineIndex] = "$key=$value"; // Update the line with new value
                file_put_contents($envFile, implode(PHP_EOL, $lines) . PHP_EOL);
                return $value; // Return the newly set value
            }
        } else {
            $lines[] = "$key=$value"; // Add new key-value pair
            file_put_contents($envFile, implode(PHP_EOL, $lines) . PHP_EOL);
            return $value; // Return the newly set value
        }

        // This point should not be reached
        return null; // Fallback return
    }
}