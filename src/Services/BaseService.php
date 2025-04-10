<?php

declare(strict_types=1);

namespace Twovmodules\RevenueCat\Services;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Twovmodules\RevenueCat\Configuration;
use Twovmodules\RevenueCat\Http\HttpClient;
use UnexpectedValueException;

abstract class BaseService
{
    protected ResponseInterface $response;

    public function __construct(
        protected HttpClient $httpClient,
        protected Configuration $configuration,
        protected RequestFactoryInterface $requestFactory,
        protected StreamFactoryInterface $streamFactory,
        protected LoggerInterface $logger = new NullLogger()
    ) {
    }

    protected function sendRequest(string $method, string $uri, ?array $body = null): array
    {
        $request = $this->requestFactory->createRequest($method, $uri);

        if ($body !== null) {
            $jsonEncodedBody = json_encode($body, JSON_THROW_ON_ERROR);
            $stream = $this->streamFactory->createStream($jsonEncodedBody);
            $request = $request->withBody($stream);
        }

        $this->response = $this->httpClient->send($request);

        return $this->decodeResponse($this->response);
    }

    protected function decodeResponse(ResponseInterface $response): array
    {
        $content = $response->getBody()
            ->getContents();

        if ($content === '' || $content === '0') {
            return [];
        }

        $decodedContent = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($decodedContent)) {
            throw new UnexpectedValueException('Decoded content is not an array');
        }

        return $decodedContent;
    }

    protected function buildNextPageUrl(string $endpoint, array $queryParams = []): string
    {
        $query = http_build_query(array_filter($queryParams));

        return $endpoint . ('?' . $query);
    }
}
