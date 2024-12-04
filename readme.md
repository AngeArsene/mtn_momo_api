# MTN MoMo Payment Gateway

This project provides a PHP-based integration with the MTN Mobile Money (MoMo) API. It facilitates seamless payment processing, enabling users to make transactions using MTN MoMo.



## Momo Api Links

  * [Momo API Production](https://momodeveloper.mtn.com/)
  * [Momo API Sandbox](https://momodeveloper.mtn.com/sandbox/)
  * [Momo API Documentation](https://momodeveloper.mtn.com/docs/services/collection/operations/requesttopay-POST)


## Requirements

  * PHP 7.0 or higher
  * CURL
  * OpenSSL
  * Momo API account

## Momo API Product 
  
   * Collection

   * Collection Widget

   * Disbursement

   * Remittances

## Collection

Collection is a payment gateway that allows you to receive payments from your customers. This API is used to integrate Momo API into your website or application.

Subscribe link : https://momodeveloper.mtn.com/products/collections/subscribe

## Collection Widget

Collection Widget is a payment gateway that allows you to receive payments from your customers. This API is used to integrate Momo API into your website or application.

## Disbursement

Disbursement is a payment gateway that allows you to receive payments from your customers. This API is used to integrate Momo API into your website or application.

## Remittances

Remittances is a payment gateway that allows you to receive payments from your customers. This API is used to integrate Momo API into your website or application.


Generate API User and API Key

You are now almost ready to start we building with our Mobile Money Open API. The next thing we need to do is to Provision the API User and API Key using the Sandbox Provisioning API. We do this in the next section.

## Features

- **Simple API Integration**: Easily integrate MTN MoMo payment functionalities into your application.
- **Secure Transactions**: Built-in security measures to ensure safe payment processing.
- **UUID Generation**: Generate unique identifiers for transactions.
- **Logging**: Monitor API requests and responses for better debugging and tracking.
- **Environment Management**: Securely manage sensitive information like API keys and secrets.

## Requirements

- PHP 7.2 or higher
- Composer

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/yourusername/mtn-momo-payment-gateway.git
   cd mtn-momo-payment-gateway 
   ```

2. Install dependencies using Composer:

   ```bash
   composer install
   ```

3. Set up environment variables. Create a .env file in the project root:

   ```bash
   API_KEY=your_api_key
   API_SECRET=your_api_secret
   ```