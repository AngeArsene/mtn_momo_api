# MTN MoMo Payment Gateway

This project provides a PHP-based integration with the MTN Mobile Money (MoMo) API. It facilitates seamless payment processing, enabling users to make transactions using MTN MoMo.

## Requirements

- PHP 7.2 or higher
- Composer
- CURL
- OpenSSL
- Momo API account

## Features

- **Simple API Integration**: Easily integrate MTN MoMo payment functionalities into your application.
- **Secure Transactions**: Built-in security measures to ensure safe payment processing.
- **UUID Generation**: Generate unique identifiers for transactions.
- **Logging**: Monitor API requests and responses for better debugging and tracking.
- **Environment Management**: Securely manage sensitive information like API keys and secrets.

## Momo API Links

- [Momo API Production](https://momodeveloper.mtn.com/)
- [Momo API Sandbox](https://momodeveloper.mtn.com/sandbox/)
- [Momo API Documentation](https://momodeveloper.mtn.com/docs/services/collection/operations/requesttopay-POST)

## Momo API Products

- **Collection**: Receive payments from customers.
- **Collection Widget**: Integrate payment gateway into your website or application.
- **Disbursement**: Send payments to customers.
- **Remittances**: Manage cross-border payments.

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/yourusername/mtn-momo-payment-gateway.git
   cd mtn-momo-payment-gateway 