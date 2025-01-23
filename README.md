# Order & Payment Management API

This project provides a RESTful API for order and payment management using Laravel. The API supports multiple payment gateways and follows best practices for security, validation, and extensibility.


## Installation

1. **Clone the repository:**

   ```bash
   git clone https://github.com/ahmednaserdev/OrderPaymentHub.git
   cd OrderPaymentHub
   ```

2. **Install dependencies:**

   ```bash
   composer install
   ```

3. **Copy the environment file and set configuration:**

   ```bash
   cp .env.example .env
   ```

   - Set database credentials
   - Configure payment gateway API keys Like:

   ```env
   NEW_GATEWAY_API_KEY= ADD HERE API KEY
   NEW_GATEWAY_API_SECRET=ADD HERE API SECRET
   ```

4. **Generate application key:**

   ```bash
   php artisan key:generate
   ```

5. **Run migrations:**

   ```bash
   php artisan migrate
   ```

6. **Start the development server:**

   ```bash
   php artisan serve
   ```

## Running Tests

To run unit and feature tests:

```bash
php artisan test
```

## Postman Collection

- Import the provided Postman collection from `OrderPaymentHub/postman/Order & Payment - Managment.postman_collection.json` to explore API endpoints.

## API Endpoints

### Authentication

| Method | Endpoint      | Description         |
| ------ | ------------- | ------------------- |
| POST   | /api/login    | Authenticate user   |
| POST   | /api/register | Register a new user |
| POST   | /api/logout | Logout a User |

### Orders

| Method | Endpoint    | Description        |
| ------ | ----------- | ------------------ |
| GET    | /api/orders | Get all orders     |
| GET    | /api/orders?status=pending | Retrieve all orders or filter (pending, confirmed, canceled) |
| POST   | /api/orders | Create a new order |
| PUT   | /api/orders | Update a order |
| DELETE   | /api/orders/{ORDER_ID} | Update a order |

### Payments

| Method | Endpoint      | Description       |
| ------ | ------------- | ----------------- |
| GET   | /api/payments | Get all payments |
| GET   | /api/payments?order_id={ORDER_ID} | Retrieve payment details for a specific order |
| POST   | /api/payments/process | Process a payment |

## Adding a New Payment Gateway

To add a new payment gateway:

1. **Create a new gateway class in ****`app/Services/PaymentGateway/`****:**

   ```php
   namespace App\Services\PaymentGateway;

   class NewGateway extends BasePaymentGateway
   {
       protected function setCredentials(): void
       {
           $this->apiKey = env('NEW_GATEWAY_API_KEY');
           $this->apiSecret = env('NEW_GATEWAY_API_SECRET');
       }

       private function authenticate(): bool
       {
           return true;
       }

       public function processPayment($order): array
       {
           if (!$this->authenticate()) {
               return ['status' => 'failed', 'message' => 'Invalid credentials', 'transaction_id' => null];
           }
           return $this->executePaymentProcess();
       }
   }
   ```

2. **Register the gateway in ****`PaymentGatewayFactory`****:**

   ```php
   public static function create(string $gateway): PaymentGatewayInterface
   {
       return match (strtolower($gateway)) {
           'paypal' => new PaypalGateway(),
           'credit_card' => new CreditCardGateway(),
           'new_gateway' => new NewGateway(),
           default => throw new InvalidArgumentException('Invalid payment gateway selected'),
       };
   }
   ```

## Security and Protection

### CORS Allowed Origins

To allow cross-origin requests, configure the allowed origins in the `.env` file:

```env
# CORS ALLOWED ORIGINS
# If you want to allow all domains, you can set it to *
# Example: CORS_ALLOWED_ORIGINS=*
CORS_ALLOWED_ORIGINS=http://127.0.0.1:8000,http://localhost:8000,http://127.0.0.1
```

## Contribution Guidelines

- Follow PSR-12 coding standards.
- Use feature branches for new features.
- Submit pull requests with clear descriptions.

## License

This project is open-source and available under the MIT license.

---

Following these guidelines will ensure a well-structured, maintainable, and scalable API project.
