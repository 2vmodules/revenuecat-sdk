# RevenueCat PHP SDK

## 📌 Description

Usage for applied services in SDK

### 1. Initialize the Client

#### Guzzle example client
```php
use Twovmodules\RevenueCat\RevenueCatClient;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;

$configuration = new Configuration('_your_api_key_here_');

$httpClient = new Client();
$requestFactory = new HttpFactory;
$streamFactory = new HttpFactory;

$logger = new Logger('revenuecat');
$logger->pushHandler(new StreamHandler('tests/revenuecat.log', Logger::DEBUG));

$revenueCat = new RevenueCatClient(
    httpClient: $httpClient,
    configuration: $configuration,
    requestFactory: $requestFactory,
    streamFactory: $streamFactory,
    logger: $logger
);

```

#### Symfony example Client

```php
use Twovmodules\RevenueCat\RevenueCatClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Psr18Client;

$symfonyHttpClient = HttpClient::create();
$symfonyPsr18Client = new Psr18Client($symfonyHttpClient);

$configuration = new Configuration('_your_api_key_here_');

$revenueCat = new RevenueCatClient(
    psrHttpClient: $symfonyPsr18Client,
    configuration: $configuration,
    apiKey: 'sk_xNFuHUSpYndByjQhueIhwVizyIqZo',
    projectId: '1664075a'
);
```

#### Usage logger

Using monolog/monolog logger or any PSR-compatable logger instead
```php
use Twovmodules\RevenueCat\RevenueCatClient;
use Monolog\Handler\StreamHandler;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Client;
use Monolog\Logger;

$httpClient = new Client();
$requestFactory = new HttpFactory;
$streamFactory = new HttpFactory;

$logger = new Logger('revenuecat');
$logger->pushHandler(new StreamHandler('path_to_log/revenuecat_debug.log', Logger::DEBUG));

$revenueCat = new RevenueCatClient(
    httpClient: $httpClient,
    apiKey: 'your_api_key',
    projectId: 'your_project_id',
    logger: $logger
);
```

#### Usage Configuration
Config can be set on initializing of Client or setting-up after initialization

```php

#minimal init without projectID
$revenueCat = new RevenueCatClient(
    httpClient: $httpClient,
    apiKey: 'your_api_key',
);

$configuration = $revenueCat->getConfiguration();

//Set project ID
$configuration->setProjectId('my_project_id');

//Change baseUrl
$configuration->withBaseUrl('https://newurl.api.com');
```

## Services
List of all available methods and return data you can find in [RevenueCat Rest v2 Documentation](https://www.revenuecat.com/docs/api-v2)

Example usage

```php
/**
 * Get List of project ID Apps 
 */
$list = $revenueCat->apps()->list('my_project_id');

/**
 * Get App by id
 */
$list = $revenueCat->apps()->list('app_id');

/**
 * Create App
 */
 $createDto = CreateApp::fromArray([
    'name' => 'My Test PlayStore',
    'type' => 'play_store',
    'play_store' => [
        'package_name' => 'test.package.name'
    ]
]);

$createApp = $revenueCatClient->apps()->create($createDto, 'my_project_id');
print_r($createApp);
```

### Apps service
**List of methods:**

```php
# Get a list of apps
list(
    string $project_id, 
    ?int $limit, 
    ?string $startingAfter
) 

# Get an app by ID
get(string $appId, string $project_id)

# Create an app
create(CreateAppDto $requestDto, string $projectId)

# Update an app
update(
    string $appId, 
    UpdateAppDto $requestDto, 
    string $projectId
)

# Delete an app by ID
delete(string $appId, string $projectId)
```
### Projects service
**List of methods:**

```php
# Get a list of projects
list(
    ?int $limit = null, 
    ?string $startingAfter = null
)
```

### Customers service
**List of methods:**

```php
# Get a list of customers
list(
    string $projectId, 
    ?int $limit, 
    ?string $startingAfter
)

# Get a customer by ID
get(string $customerId, string $projectId)

# Create a new customer
create(
    string $newCustomerId, 
    ?array $attributes, 
    string $projectId
)

# Delete a customer by ID
delete(string $customerId, string $projectId)

# Get a list of subscriptions for a customer
getSubscriptions(
    string $customerId, 
    string $projectId, 
    ?EnvironmentType $environmentType, 
    ?int $limit, 
    ?string $startingAfter
)

# Get a list of purchases for a customer
getPurchases(
    string $customerId, 
    string $projectId, 
    ?int $limit, 
    ?string $startingAfter
)

# Get a list of active entitlements for a customer
getEntitlements(
    string $customerId, 
    string $projectId, 
    ?int $limit, 
    ?string $startingAfter
)

# Get a list of aliases for a customer
getAliases(string $customerId, string $projectId)

# Get a list of attributes for a customer
getAttributes(
    string $customerId, 
    string $projectId, 
    ?int $limit, 
    ?string $startingAfter
)

# Create attributes for a customer
createAttributes(
    array $attributes, 
    string $customerId, 
    string $projectId
)
```

### Offerings service
**List of methods:**

```php
# Get a list of offerings
list(
    string $projectId, 
    ?bool $expand,
    ?int $limit, 
    ?string $startingAfter
) 

# Get an offering by ID
get(string $offeringId, string $projectId, ?bool $expand) 

# Create a new offering
create(CreateOfferingDto $requestDto, string $projectId) 

# Update an offering
update(
    string $offeringId, 
    UpdateOfferingDto $requestDto, 
    string $projectId
) 

# Delete an offering by ID
delete(string $offeringId, string $projectId) 
```

### Subscriptions service
**List of methods:**

```php
# Get a list of entitlements for a subscription
list(
    string $subscriptionId, 
    string $projectId, 
    ?int $limit, 
    ?string $startingAfter
)

# Get a subscription by ID
get(string $subscriptionId, string $projectId)

# Cancel a subscription
cancel(string $subscriptionId, string $projectId)

# Refund a subscription
refund(string $subscriptionId, string $projectId)
```

### Entitlements service
**List of methods:**

```php
# Get a list of entitlements
list(
    string $projectId, 
    ?bool $expand, 
    ?int $limit, 
    ?string $startingAfter
)

# Get an entitlement by ID
get(
    string $entitlementId, 
    string $projectId,
    ?bool $expand
)

# Create a new entitlement
create(CreateEntitlementDto $requestDto, string $projectId)

# Update an entitlement
update(
    string $entitlementId, 
    UpdateEntitlementDto $requestDto, 
    string $projectId
)

# Delete an entitlement by ID
delete(string $entitlementId, string $projectId)

# Get a list of products for an entitlement
getProducts(
    string $entitlementId, 
    string $projectId, 
    ?int $limit, 
    ?string $startingAfter
)

# Attach products to an entitlement
attachProduct(
    string $entitlementId, 
    array $productIds, 
    string $projectId
)

# Detach products from an entitlement
detachProduct(
    string $entitlementId, 
    array $productIds, 
    string $projectId
)
```

### Products service
**List of methods:**

```php
# Get a list of products
list(
    ?string $appId = null, 
    ?bool $expand = null, 
    string $projectId, 
    ?int $limit = null, 
    ?string $startingAfter = null
)

# Get a product by ID
get(string $productId, string $projectId)

# Create a new product
create(CreateProductDto $requestDto = null, string $projectId)

# Delete a product by ID
delete(string $productId, string $projectId)
```

### Invoices service
**List of methods:**

```php
# Get a list of invoices for a customer
list(
    string $customerId, 
    string $projectId, 
    ?int $limit = null, 
    ?string $startingAfter = null
)

# Get an invoice by ID
get(
    string $customerId, 
    string $invoiceId, 
    string $projectId
)
```

### OverviewMetrics service
**List of methods:**

```php
# Get an overview of metrics for a project
get(string $projectId)
```

### Packages service
**List of methods:**

```php
# Get a list of packages for an offering
list(
    string $offeringId, 
    ?bool $expand = null, 
    string $projectId, 
    ?int $limit = null, 
    ?string $startingAfter = null
)

# Get a package by ID
get(
    string $packageId, 
    ?bool $expand = false, 
    string $projectId
)

# Create a new package for an offering
create(
    string $offeringId, 
    CreatePackageDto $requestDto, 
    string $projectId
)

# Update a package
update(
    string $packageId, 
    UpdatePackageDto $requestDto, 
    string $projectId
)

# Delete a package by ID
delete(string $packageId, string $projectId)

# Get a list of products for a package
getProducts(
    string $packageId, 
    string $projectId, 
    ?int $limit = null, 
    ?string $startingAfter = null
)

# Attach products to a package
attachProduct(
    string $packageId, 
    array $products, 
    string $projectId
)

# Detach products from a package
detachProduct(
    string $packageId, 
    array $productIds, 
    string $projectId
)
```

### Purchases service
**List of methods:**

```php
# Get a list of entitlements for a purchase
list(
    string $purchaseId, 
    string $projectId, 
    ?int $limit = null, 
    ?string $startingAfter = null
)

# Get a purchase by ID
get(string $purchaseId, string $projectId)

# Refund a purchase
refund(string $purchaseId, string $projectId)
```
