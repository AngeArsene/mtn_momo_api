<?php 

namespace MtnMomoPaymentGateway\Core;

use Dotenv\Dotenv;
use Ramsey\Uuid\Uuid;

class Application 
{
    public static string $HOME_DIR;
    
    protected static string $PRIMARY_KEY;
    protected static string $SECONDARY_KEY;

    public function __construct() 
    {
        self::$HOME_DIR = dirname(dirname(__DIR__));

        $this->bootstrap();

        var_dump($this->user_reference_id());
    }

    private function bootstrap(): void
    {
        self::$PRIMARY_KEY   = $this->env()->primary_key;
        self::$SECONDARY_KEY = $this->env()->secondary_key;
        
        
    }

    private function env(): object
    {
        $dotenv = Dotenv::createImmutable(self::$HOME_DIR);
        $dotenv->load();

        return (object) $_ENV;
    }

    protected function user_reference_id(): string
    {
        $uuid = Uuid::uuid4();

        return $this->write_to_env('user_reference_id', $uuid);
    }

    private function write_to_env(string $key, string $value): string
    {
        $envFile = self::$HOME_DIR . DIRECTORY_SEPARATOR .'.env';

        // Read the existing content of the .env file
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        // Initialize a variable to store the found value
        $foundValue = null;
        $found = false;

        // Search for the key in the existing lines
        foreach ($lines as &$line) {
            if (strpos($line, $key . '=') === 0) {
                $foundValue = trim(explode('=', $line, 2)[1]); // Get the value
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
                $lines[$lineIndex] = "$key=$value"; // Update the line
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
