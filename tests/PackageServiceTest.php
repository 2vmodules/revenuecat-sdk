<?php

namespace Twovmodules\RevenueCat\Tests;

use GuzzleHttp\Psr7\HttpFactory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\NullLogger;
use Twovmodules\RevenueCat\Configuration;
use Twovmodules\RevenueCat\Dto\Paginator;
use Twovmodules\RevenueCat\Dto\Request\AttachPackageProduct;
use Twovmodules\RevenueCat\Dto\Request\CreatePackage;
use Twovmodules\RevenueCat\Dto\Request\UpdatePackage;
use Twovmodules\RevenueCat\Dto\Response\Package;
use Twovmodules\RevenueCat\Dto\Response\PackageProduct;
use Twovmodules\RevenueCat\Enum\EligibilityCriteria;
use Twovmodules\RevenueCat\Http\HttpClient;
use Twovmodules\RevenueCat\Services\PackageService;

class PackageServiceTest extends TestCase
{
    public function testGetPackagesList(): void
    {
        $data = [
            'items' => [
                [
                    'id' => 'pkg1',
                    'lookup_key' => 'key1',
                    'display_name' => 'Package 1',
                    'created_at' => time(),
                    'position' => 1,
                ],
                [
                    'id' => 'pkg2',
                    'lookup_key' => 'key2',
                    'display_name' => 'Package 2',
                    'created_at' => time(),
                    'position' => 2,
                ],
            ],
            'next_page' => 'nextPageToken',
            'url' => null,
        ];

        $packageService = $this->getService($data);
        $paginator = $packageService->list('offering1', 'proj1');

        $this->assertInstanceOf(Paginator::class, $paginator);
        $this->assertInstanceOf(Package::class, $paginator->items[0]);
        $this->assertCount(2, $paginator->items);
        $this->assertSame('nextPageToken', $paginator->nextPage);
    }

    public function testGetPackageById(): void
    {
        $data = [
            'id' => 'pkg1',
            'lookup_key' => 'key1',
            'display_name' => 'Package 1',
            'created_at' => time(),
            'position' => 1,
        ];

        $packageService = $this->getService($data);
        $package = $packageService->get('pkg1', 'proj1');

        $this->assertInstanceOf(Package::class, $package);
        $this->assertSame('pkg1', $package->id);
    }

    public function testCreatePackage(): void
    {
        $data = [
            'id' => 'pkg1',
            'lookup_key' => 'key1',
            'display_name' => 'Package 1',
            'created_at' => time(),
            'position' => 1,
        ];

        $packageService = $this->getService($data);
        $createPackage = new CreatePackage('key1', 'Package 1');

        $package = $packageService->create('offering1', $createPackage, 'proj1');

        $this->assertInstanceOf(Package::class, $package);
        $this->assertSame('Package 1', $package->displayName);
    }

    public function testUpdatePackage(): void
    {
        $data = [
            'id' => 'pkg1',
            'lookup_key' => 'key1',
            'display_name' => 'Updated Package 1',
            'created_at' => time(),
            'position' => 2,
        ];

        $packageService = $this->getService($data);
        $updatePackage = new UpdatePackage('Updated Entitlement', 2);

        $package = $packageService->update('ent1', $updatePackage, 'proj1');

        $this->assertInstanceOf(Package::class, $package);
        $this->assertSame('Updated Package 1', $package->displayName);
    }

    public function testDeletePackage(): void
    {
        $data = [];

        $packageService = $this->getService($data);
        $this->expectNotToPerformAssertions();
        $packageService->delete('ent1', 'proj1');
    }

    public function testGetProductsForPackageOfEntitlement(): void
    {

        $data = [
            'items' => [
                [
                    'product' => [
                        'id' => 'prod1',
                        'display_name' => 'Product 1',
                        'store_identifier' => 'prod1',
                        'type' => 'one_time',
                        'created_at' => time(),
                        'app_id' => 'app1',
                    ],
                    'eligibility_criteria' => EligibilityCriteria::GOOGLE_SDK_GE_6->value,
                ],
            ],
            'next_page' => null,
            'url' => null,
        ];

        $packageService = $this->getService($data);
        $paginator = $packageService->getProducts('pkg1', 'proj1');

        $this->assertInstanceOf(Paginator::class, $paginator);
        $this->assertInstanceOf(PackageProduct::class, $paginator->items[0]);
    }

    public function testAttachProductToPackage(): void
    {
        $data = [
            'id' => 'pkg1',
            'lookup_key' => 'key1',
            'display_name' => 'Package 1',
            'created_at' => time(),
            'position' => 1,
        ];

        $packageService = $this->getService($data);
        $package = $packageService->attachProduct('pkg1', [
            AttachPackageProduct::fromArray([
                'product_id' => 'prod1',
                'eligibility_criteria' => 'google_sdk_ge_6',
            ]),
        ], 'proj1');

        $this->assertInstanceOf(Package::class, $package);
    }

    public function testDetachProductFromEntitlement(): void
    {
        $data = [
            'id' => 'pkg1',
            'lookup_key' => 'key1',
            'display_name' => 'Package 1',
            'created_at' => time(),
            'position' => 1,
        ];

        $packageService = $this->getService($data);
        $package = $packageService->detachProduct('pkg1', ['prod1'], 'proj1');

        $this->assertInstanceOf(Package::class, $package);
    }

    private function createMockClient(ResponseInterface $response): ClientInterface
    {
        $mockClient = $this->createMock(ClientInterface::class);
        $mockClient->method('sendRequest')
            ->willReturn($response);

        return $mockClient;
    }

    private function createMockStream(string $content): StreamInterface
    {
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('getContents')
            ->willReturn($content);

        return $stream;
    }

    private function getService(array $data): PackageService
    {
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getBody')
            ->willReturn($this->createMockStream(json_encode($data, JSON_THROW_ON_ERROR)));

        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getBaseUrl')
            ->willReturn('https://api.example.com');
        $configuration->method('getApiKey')
            ->willReturn('test_api_key');

        $httpClient = new HttpClient($this->createMockClient($mockResponse), $configuration, new NullLogger());

        return new PackageService(
            $httpClient,
            $configuration,
            new HttpFactory(),
            new HttpFactory(),
            new NullLogger()
        );
    }
}
