# MTN MoMo Payment Gateway

This project provides a PHP-based integration with the MTN Mobile Money (MoMo) API. It facilitates seamless payment processing, enabling users to make transactions using MTN MoMo.

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