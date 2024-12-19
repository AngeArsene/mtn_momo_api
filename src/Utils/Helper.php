<?php

namespace MtnMomoPaymentGateway\Utils;

use Dotenv\Dotenv;
use MtnMomoPaymentGateway\Core\Application;

/**
 * Class Helper
 * 
 * Provides utility functions for handling environment variables and configuration.
 */
final class Helper 
{

    /**
     * Get the path to the .env file
     *
     * @return string The path to the .env file
     */
    private function env_file(): string
    {
        return Application::$HOME_DIR . DIRECTORY_SEPARATOR . '.env';
    }

    /**
     * Loads environment variables from the .env file
     *
     * @return object An object containing environment variables
     */
    public static function env(): object
    {
        $dotenv = Dotenv::createImmutable(Application::$HOME_DIR);
        $dotenv->load();

        return (object) $_ENV;
    }

    /**
     * Writes a key-value pair to the .env file
     *
     * @param string $key The key to write
     * @param string $value The value to write
     * @return string The value that was written
     */
    public static function write_to_env(string $key, string $value): string
    {
        $envFile = (new self())->env_file();

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
    }

    /**
     * Check if a key is present in the environment variables and is set to a specific value
     * 
     * @param string $key The key to check
     * @return bool True if the key is present and set to the expected value, false otherwise
     */
    public static function is_env_key_set(string $key): bool
    {
        return isset($_ENV[$key]) && $_ENV[$key] !== "";
    }

    /**
     * Remove a key and its value from the environment variables
     * 
     * @param string $key The key to remove
     */
    public static function remove_env_key(string $key): void
    {
        $envFile = (new self())->env_file();

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

        // If the key was found, remove it
        if ($found) {
            $lineIndex = array_search($line, $lines);
            $lines[$lineIndex] = ""; // Remove the line

            file_put_contents($envFile, implode(PHP_EOL, $lines) . PHP_EOL);
        }
    }
}