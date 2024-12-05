<?php

require './vendor/autoload.php';

use MtnMomoPaymentGateway\Core\Application;

/**
 * Entry point for the MtnMomo Payment Gateway application
 */

// Instantiate the main Application class to initialize the MtnMomo Payment Gateway
$application = new Application();

// Make a request to pay a specified amount to the provided partyId
$response = $application->request_to_pay('100', '+237675962178');

// Output the response for debugging purposes
var_dump($response);