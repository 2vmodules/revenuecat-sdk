<?php

namespace Twovmodules\RevenueCat\Tests;

use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\NullLogger;
use Twovmodules\RevenueCat\Configuration;
use Twovmodules\RevenueCat\Dto\Paginator;
use Twovmodules\RevenueCat\Dto\Response\Invoice;
use Twovmodules\RevenueCat\Dto\Response\InvoiceFile;
use Twovmodules\RevenueCat\Http\HttpClient;
use Twovmodules\RevenueCat\Services\InvoiceService;

class InvoiceServiceTest extends TestCase
{
    public function testListInvoices(): void
    {
        $data = [
            'items' => [
                [
                    'id' => 'invoice1',
                    'total_amount' => 100.0,
                    'line_items' => [],
                    'issued_at' => time(),
                ],
                [
                    'id' => 'invoice2',
                    'total_amount' => 200.0,
                    'line_items' => [],
                    'issued_at' => time(),
                ],
            ],
            'next_page' => 'nextPageToken',
            'url' => 'https://api.example.com/projects/test_project_id/customers/customer1/invoices',
        ];

        $invoiceService = $this->getService($data);
        $paginator = $invoiceService->list('customer1', 'proj1');

        $this->assertInstanceOf(Paginator::class, $paginator);
        $this->assertInstanceOf(Invoice::class, $paginator->items[0]);
        $this->assertCount(2, $paginator->items);
        $this->assertSame('nextPageToken', $paginator->nextPage);
    }

    public function testGetInvoiceById(): void
    {
        $mockResponse = new Response(302, [
            'location' => 'test_location',
        ]);

        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getBaseUrl')
            ->willReturn('https://api.example.com');
        $configuration->method('getApiKey')
            ->willReturn('test_api_key');

        $httpClient = new HttpClient($this->createMockClient($mockResponse), $configuration, new NullLogger());

        $invoiceService = new InvoiceService(
            $httpClient,
            $configuration,
            new HttpFactory(),
            new HttpFactory(),
            new NullLogger()
        );

        $invoiceFile = $invoiceService->get('customer1', 'invoice1', 'proj1');

        $this->assertInstanceOf(InvoiceFile::class, $invoiceFile);
        $this->assertObjectHasProperty('location', $invoiceFile);
        $this->assertSame('test_location', $invoiceFile->location);
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

    private function getService(array $data): InvoiceService
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

        return new InvoiceService(
            $httpClient,
            $configuration,
            new HttpFactory(),
            new HttpFactory(),
            new NullLogger()
        );
    }
}
