# SJOPPIE API
> E-commerce Backend System

[![Latest Version](https://img.shields.io/github/v/release/CODEIQBV/sjoppie-api?style=flat-square)](https://github.com/CODEIQBV/sjoppie-api/releases)
[![License](https://img.shields.io/github/license/CODEIQBV/sjoppie-api?style=flat-square)](LICENSE.md)
[![PHP Version](https://img.shields.io/badge/php-8.1%2B-blue?style=flat-square)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/laravel-10.x-orange?style=flat-square)](https://laravel.com)

The Sjoppie API is a robust e-commerce backend system developed by CodeIQ B.V. It provides a comprehensive set of RESTful endpoints for managing products, orders, payments, and customer data.

## Features

- **Product Management**: Full CRUD operations for products, variants, prices, and stock
- **Order Processing**: Complete order lifecycle management with payment integration
- **Customer Management**: Customer profiles with address management
- **Payment Integration**: Multiple payment gateway support with webhook handling
- **Stock Control**: Advanced stock management with available, on-hand, and reserved quantities
- **Authentication**: Secure API key and token-based authentication
- **Rate Limiting**: Built-in rate limiting for API protection

## Technology Stack

- PHP 8.1+
- Laravel 10.x
- MySQL/PostgreSQL
- RESTful API architecture
- JWT Authentication
- Payment Gateway Integration (Mollie, etc.)

## Documentation

Detailed API documentation is available in the [docs](docs) directory:

- [API Overview](docs/api.md)
- [Authentication](docs/users.md)
- [Products](docs/products.md)
- [Orders](docs/orders.md)
- [Payments](docs/payment.md)
- [Stock Management](docs/stock.md)
- [Workflow](docs/workflow.md)

## Security

- API key authentication required for all endpoints
- Rate limiting enabled by default
- CSRF protection for web routes
- Secure password hashing
- Input validation and sanitization

## License

This project is proprietary software developed by CodeIQ B.V. All rights reserved.
