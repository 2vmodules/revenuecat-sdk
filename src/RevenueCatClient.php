<?php

declare(strict_types=1);

namespace Twovmodules\RevenueCat;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Twovmodules\RevenueCat\Http\HttpClient;
use Twovmodules\RevenueCat\Services\AppService;
use Twovmodules\RevenueCat\Services\BaseService;
use Twovmodules\RevenueCat\Services\CustomerService;
use Twovmodules\RevenueCat\Services\EntitlementService;
use Twovmodules\RevenueCat\Services\InvoiceService;
use Twovmodules\RevenueCat\Services\OfferingService;
use Twovmodules\RevenueCat\Services\OverviewMetricsService;
use Twovmodules\RevenueCat\Services\PackageService;
use Twovmodules\RevenueCat\Services\ProductService;
use Twovmodules\RevenueCat\Services\ProjectService;
use Twovmodules\RevenueCat\Services\PurchaseService;
use Twovmodules\RevenueCat\Services\SubscriptionService;

class RevenueCatClient
{
    private readonly HttpClient $httpClient;

    public function __construct(
        private readonly ClientInterface $psrHttpClient,
        private readonly Configuration $configuration,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly LoggerInterface $logger = new NullLogger()
    ) {
        $this->httpClient = new HttpClient($this->psrHttpClient, $this->configuration, $logger);
    }

    public function projects(): ProjectService
    {
        return $this->makeService(ProjectService::class);
    }

    public function apps(): AppService
    {
        return $this->makeService(AppService::class);
    }

    public function customers(): CustomerService
    {
        return $this->makeService(CustomerService::class);
    }

    public function offerings(): OfferingService
    {
        return $this->makeService(OfferingService::class);
    }

    public function entitlements(): EntitlementService
    {
        return $this->makeService(EntitlementService::class);
    }

    public function invoices(): InvoiceService
    {
        return $this->makeService(InvoiceService::class);
    }

    public function overviewMetrics(): OverviewMetricsService
    {
        return $this->makeService(OverviewMetricsService::class);
    }

    public function packages(): PackageService
    {
        return $this->makeService(PackageService::class);
    }

    public function products(): ProductService
    {
        return $this->makeService(ProductService::class);
    }

    public function purchases(): PurchaseService
    {
        return $this->makeService(PurchaseService::class);
    }

    public function subscriptions(): SubscriptionService
    {
        return $this->makeService(SubscriptionService::class);
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    /**
     * @template T of BaseService
     *
     * @param  class-string<T>  $serviceClass
     * @return T
     */
    private function makeService(string $serviceClass): BaseService
    {
        return new $serviceClass(
            $this->httpClient,
            $this->configuration,
            $this->requestFactory,
            $this->streamFactory,
            $this->logger,
        );
    }
}
