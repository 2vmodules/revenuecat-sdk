<?php

declare(strict_types=1);

namespace Twovmodules\RevenueCat\Services;

use Twovmodules\RevenueCat\Dto\Paginator;
use Twovmodules\RevenueCat\Dto\Request\CreateProduct;
use Twovmodules\RevenueCat\Dto\Response\Product;

class ProductService extends BaseService
{
    /**
     * Lists products with optional filters.
     *
     * @param  string  $projectId  The project ID to filter products by.
     * @param  string|null  $appId  The application ID to filter products by.
     * @param  bool|null  $expand  Whether to expand the product details.
     * @param  int|null  $limit  The maximum number of products to return.
     * @param  string|null  $startingAfter  The cursor for pagination, indicating the starting point.
     * @return Paginator<Product> A paginator instance containing the list of products.
     */
    public function list(
        string $projectId,
        ?string $appId = null,
        ?bool $expand = null,
        ?int $limit = null,
        ?string $startingAfter = null
    ): Paginator {
        $endpoint = sprintf('/projects/%s/products', $projectId);

        if ($limit !== null || $startingAfter !== null || $expand !== null || $appId !== null) {
            $endpoint = $this->buildNextPageUrl($endpoint, [
                'limit' => $limit,
                'starting_after' => $startingAfter,
                'expand' => $expand,
                'app_id' => $appId,
            ]);
        }

        $response = $this->sendRequest('GET', $endpoint);

        $response['items'] = array_map(static fn ($item): Product => Product::fromArray($item), $response['items'] ?? []);

        return new Paginator($response['items'], $response['next_page'], $response['url']);
    }

    /**
     * Retrieves a product by its ID and project ID.
     *
     * @param  string  $productId  The ID of the product to retrieve.
     * @param  string  $projectId  The ID of the project to which the product belongs.
     * @return Product The product corresponding to the given IDs.
     */
    public function get(string $productId, string $projectId): Product
    {
        $endpoint = sprintf('/projects/%s/products/%s', $projectId, $productId);
        $response = $this->sendRequest('GET', uri: $endpoint);

        return Product::fromArray($response);
    }

    /**
     * Creates a new product.
     *
     * @param  CreateProduct  $requestDto  Data transfer object containing the product creation details.
     * @param  string  $projectId  The ID of the project to which the product belongs.
     * @return Product The created product.
     */
    public function create(CreateProduct $requestDto, string $projectId): Product
    {
        $this->logger->warning("This endpoint does not allow to create Web Billing products.
        \nThis endpoint requires the following permission(s): 'project_configuration:products:read_write'.");

        $endpoint = sprintf('/projects/%s/products', $projectId);

        $response = $this->sendRequest('POST', $endpoint, $requestDto->toArray());

        return Product::fromArray($response);
    }

    /**
     * Deletes a product by its ID within a specified project.
     *
     * @param  string  $productId  The ID of the product to delete.
     * @param  string  $projectId  The ID of the project containing the product.
     */
    public function delete(string $productId, string $projectId): void
    {
        $endpoint = sprintf('/projects/%s/products/%s', $projectId, $productId);
        $this->sendRequest('DELETE', $endpoint);
    }
}
