<?php

require './vendor/autoload.php';

use MtnMomoPaymentGateway\Core\Application;

/**
 * Entry point for the MtnMomo Payment Gateway application
 */

// Instantiate the main Application class to initialize the MtnMomo Payment Gateway
$application = new Application();

// Generate a random amount between 1000 and 100000
$amount = ''.rand(1000, 100000);

// Generate a random party ID with the format +23767XXXXXXX
$partyId = '+23767'.rand(1000000, 9999999);

// Make a request to pay the specified amount to the generated partyId
$response = $application->request_to_pay($amount, $partyId);

// Output the response for debugging purposes
var_dump($response->get_transaction_status());