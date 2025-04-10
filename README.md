<p align="center"><a href="https://2vmodules.com" target="_blank">
    <img src="https://2vmodules.com/images/logo.svg" alt="2Vmodules Logo">
</a></p>
# RevenueCat PHP SDK

## 📌 Description

This SDK provides a convenient interface for working with the RevenueCat API, following PSR standards. It supports managing projects, customers, subscriptions, entitlements, and other RevenueCat entities.

## 🚀 Installation

```sh
composer require Twovmodules/revenuecat-sdk
```

## 🛠 Minimum Requirements

- PHP 8.2+
- PSR-7, PSR-17, PSR-18 compatible HTTP client (e.g., Guzzle)

## 📖 Quick Start

### 1. Initialize the Minimal Client

```php
use Twovmodules\RevenueCat\RevenueCatClient;
use Twovmodules\RevenueCat\Configuration;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Client;

$configuration = new Configuration('_your_api_key_here_');

$guzzleClient = new Client;
$requestFactory = new HttpFactory();
$streamFactory = new HttpFactory();

$revenueCat = new RevenueCatClient(
    psrHttpClient: $guzzleClient,
    configuration: $configuration,
    requestFactory: $requestFactory,
    streamFactory: $streamFactory
);
```

### 2. Retrieve a List of Customers

```php
$customers = $revenueCat->customers()->list('project_id');
foreach ($customers as $customer) {
    echo $customer->id . "\n";
}
```

### 3. Create a New Customer

```php
use Twovmodules\RevenueCat\Dto\Request\CreateCustomerDto;

$request = new CreateCustomerDto(id: 'customer_123');
$customer = $revenueCat->customers()->create($request, 'project_id');
echo "Customer created: " . $customer->id;
```

## 📚 Available Services

- `apps()` — Operations about apps.
- `projects()` — Operations about projects.
- `customers()` — Operations about customers.
- `offerings()` — Operations about offerings.
- `subscriptions()` — Operations about subscriptions.
- `entitlements()` — Operations about entitlements.
- `products()` — Operations about products.
- `invoices()` — Operations about invoices.
- `overviewMetrics()` — Operations about chart metrics.
- `packages()` — Operations about packages.
- `purchases()` — Operations about purchases.

## ⚠️ Error Handling

```php
try {
    $apps = $revenueCat->apps()->list('project_id');
} catch (RevenueCatException $e) {
    // Handle specific RevenueCat errors
    echo "Error: " . $e->getMessage();
} catch (\Exception $e) {
    // Handle other exceptions
    echo "Unexpected error: " . $e->getMessage();
}
```

## 📖 More Information

- [Usage Documentation](docs/Usage.md)

## 📄 License
This SDK is open-sourced under MIT [License](LICENSE.MD).
