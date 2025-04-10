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
use Twovmodules\RevenueCat\Dto\Request\CreateOffering;
use Twovmodules\RevenueCat\Dto\Request\UpdateOffering;
use Twovmodules\RevenueCat\Dto\Response\Offering;
use Twovmodules\RevenueCat\Http\HttpClient;
use Twovmodules\RevenueCat\Services\OfferingService;

class OfferingServiceTest extends TestCase
{
    public function testGetOfferingsNotExpandedPaginated(): void
    {
        $data = [
            'items' => [
                [
                    'id' => 'ofr1',
                    'lookup_key' => 'default',
                    'display_name' => 'Default Offering',
                    'is_current' => true,
                    'created_at' => time(),
                    'project_id' => 'proj_test_123',
                ],
                [
                    'id' => 'ofr2',
                    'lookup_key' => 'pro',
                    'display_name' => 'Pro Offering',
                    'is_current' => false,
                    'created_at' => time(),
                    'project_id' => 'proj_test_123',
                ],
            ],
            'next_page' => 'nextPageToken',
            'url' => 'https://api.example.com/projects/proj_test_123/offerings',
        ];

        $offeringService = $this->getService($data);
        $paginator = $offeringService->list('proj1');

        $this->assertInstanceOf(Paginator::class, $paginator);
        $this->assertInstanceOf(Offering::class, $paginator->items[0]);
        $this->assertCount(2, $paginator->items);
        $this->assertSame('nextPageToken', $paginator->nextPage);

        /** @var Offering $firstOffering */
        $firstOffering = $paginator->items[0];
        $this->assertEquals('ofr1', $firstOffering->id);
        $this->assertEquals('default', $firstOffering->lookupKey);
    }

    public function testGetOfferingById(): void
    {
        $data = [
            'id' => 'ofr1',
            'lookup_key' => 'default',
            'display_name' => 'Default Offering',
            'is_current' => true,
            'created_at' => time(),
            'project_id' => 'proj_test_123',
        ];

        $offeringService = $this->getService($data);

        $offering = $offeringService->get('ofr1', 'proj1');

        $this->assertInstanceOf(Offering::class, $offering);
        $this->assertSame('ofr1', $offering->id);
        $this->assertSame('Default Offering', $offering->displayName);
    }

    public function testCreateOffering(): void
    {
        $data = [
            'id' => 'ofr_new',
            'lookup_key' => 'premium',
            'display_name' => 'Premium Offering',
            'is_current' => false,
            'created_at' => time(),
            'project_id' => 'proj_test_123',
        ];

        $offeringService = $this->getService($data);

        $createOffering = new CreateOffering(
            lookupKey: 'premium',
            displayName: 'Premium Offering',
            metadata: [
                'type' => 'advanced',
            ]
        );

        $offering = $offeringService->create($createOffering, 'proj1');

        $this->assertInstanceOf(Offering::class, $offering);
        $this->assertSame('ofr_new', $offering->id);
        $this->assertSame('Premium Offering', $offering->displayName);
    }

    public function testUpdateOffering(): void
    {
        $data = [
            'id' => 'ofr1',
            'lookup_key' => 'default',
            'display_name' => 'Updated Offering',
            'is_current' => true,
            'created_at' => time(),
            'project_id' => 'proj_test_123',
        ];

        $updateOffering = new UpdateOffering(
            displayName: 'Updated Offering',
            isCurrent: true,
            metadata: [
                'type' => 'advanced',
            ]
        );

        $offeringService = $this->getService($data);

        $offering = $offeringService->update('ofr1', $updateOffering, 'proj1');

        $this->assertInstanceOf(Offering::class, $offering);
        $this->assertSame('Updated Offering', $offering->displayName);
    }

    public function testDeleteOffering(): void
    {
        $data = [
            'object' => 'offering',
            'id' => 'ofr1',
            'deleted_at' => time(),
        ];

        $offeringService = $this->getService($data);

        $this->expectNotToPerformAssertions();
        $offeringService->delete('ofr1', 'proj1');
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

    private function getService(array $data): OfferingService
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

        return new OfferingService(
            $httpClient,
            $configuration,
            new HttpFactory(),
            new HttpFactory(),
            new NullLogger()
        );
    }
}
