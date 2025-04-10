<?php

namespace Twovmodules\RevenueCat\Http;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Twovmodules\RevenueCat\Configuration;
use Twovmodules\RevenueCat\Exceptions\ApiException;
use Twovmodules\RevenueCat\Exceptions\RateLimitException;

class HttpClient
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly Configuration $configuration,
        private readonly LoggerInterface $logger
    ) {
    }

    public function send(RequestInterface $request): ResponseInterface
    {
        $uri = $request->getUri();
        if (in_array($uri->getHost(), ['', '0'], true)) {
            $path = $uri->getPath();
            $baseUri = (string) parse_url($this->configuration->getBaseUrl(), PHP_URL_HOST);
            $baseUriPath = (string) parse_url($this->configuration->getBaseUrl(), PHP_URL_PATH);

            $uri = $request->getUri()
                ->withHost($baseUri)
                ->withScheme('https')
                ->withPath($baseUriPath . $path);
            $request = $request->withUri($uri);
        }

        $request = $request
            ->withHeader('Authorization', 'Bearer ' . $this->configuration->getApiKey())
            ->withHeader('Content-Type', 'application/json');

        $this->logger->debug('Request', [
            'method' => $request->getMethod(),
            'url' => (string) $request->getUri(),
            'headers' => $request->getHeaders(),
            'body' => (string) $request->getBody(),
        ]);

        try {
            $response = $this->client->sendRequest($request);
            $statusCode = $response->getStatusCode();

            if ($statusCode === 429) {
                $retryAfterHeader = $response->getHeader('Retry-After');
                $currentUsageHeader = $response->getHeader('RevenueCat-Rate-Limit-Current-Usage');
                $currentLimitHeader = $response->getHeader('RevenueCat-Rate-Limit-Current-Limit');

                $this->logger->error('Rate limit exceeded', [
                    'retry_after' => $retryAfterHeader,
                    'current_usage' => $currentUsageHeader,
                    'current_limit' => $currentLimitHeader,
                ]);

                throw new RateLimitException(
                    'Rate limit exceeded',
                    429,
                    $retryAfterHeader !== [] ? (int) $retryAfterHeader[0] : null,
                    $currentUsageHeader !== [] ? (int) $currentUsageHeader[0] : null,
                    $currentLimitHeader !== [] ? (int) $currentLimitHeader[0] : null
                );
            }

            if ($statusCode >= 400) {
                $errorBody = $response->getBody()
                    ->getContents();

                $this->logger->error('API request failed', [
                    'status_code' => $statusCode,
                    'error_body' => $errorBody,
                ]);

                throw new ApiException(
                    sprintf('API request failed: %s ', $errorBody) . $response->getReasonPhrase(),
                    $statusCode
                );
            }

            return $response;
        } catch (ClientExceptionInterface $clientException) {
            throw new ApiException(
                'HTTP request failed: ' . $clientException->getMessage(),
                $clientException->getCode(),
                $clientException
            );
        }
    }
}
