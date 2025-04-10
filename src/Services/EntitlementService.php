<?php

declare(strict_types=1);

namespace Twovmodules\RevenueCat\Services;

use Twovmodules\RevenueCat\Dto\Paginator;
use Twovmodules\RevenueCat\Dto\Request\CreateEntitlement;
use Twovmodules\RevenueCat\Dto\Request\UpdateEntitlement;
use Twovmodules\RevenueCat\Dto\Response\Entitlement;
use Twovmodules\RevenueCat\Dto\Response\Product;

class EntitlementService extends BaseService
{
    /**
     * Retrieves a list of entitlements for a given project.
     *
     * @param  string  $projectId  The ID of the project for which to list entitlements.
     * @param  bool|null  $expand  Optional. Whether to expand the response with additional details.
     * @param  int|null  $limit  Optional. The maximum number of entitlements to return.
     * @param  string|null  $startingAfter  Optional. A cursor for pagination.
     * @return Paginator<Entitlement> A paginator instance containing the list of entitlements.
     */
    public function list(
        string $projectId,
        ?bool $expand = null,
        ?int $limit = null,
        ?string $startingAfter = null
    ): Paginator {
        $endpoint = sprintf('/projects/%s/entitlements', $projectId);

        if ($limit !== null || $startingAfter !== null || $expand !== null) {
            $endpoint = $this->buildNextPageUrl($endpoint, [
                'limit' => $limit,
                'starting_after' => $startingAfter,
                'expand' => $expand ? 'items.product' : null,
            ]);
        }

        $response = $this->sendRequest('GET', $endpoint);
        $response['items'] = array_map(
            static fn ($item): Entitlement => Entitlement::fromArray($item),
            $response['items'] ?? []
        );

        return new Paginator($response['items'], $response['next_page'], $response['url']);
    }

    /**
     * Retrieves an entitlement by its ID and project ID.
     *
     * @param  string  $entitlementId  The ID of the entitlement to retrieve.
     * @param  string  $projectId  The ID of the project associated with the entitlement.
     * @param  bool|null  $expand  Optional. Whether to expand the entitlement details. Default is false.
     * @return Entitlement|null The entitlement object if found, or null if not found.
     */
    public function get(string $entitlementId, string $projectId, ?bool $expand = false): ?Entitlement
    {
        $endpoint = sprintf('/projects/%s/entitlements/%s', $projectId, $entitlementId);

        if ($expand) {
            $endpoint = $this->buildNextPageUrl($endpoint, [
                'expand' => 'product',
            ]);
        }

        $response = $this->sendRequest('GET', $endpoint);

        return $response === [] ? null : Entitlement::fromArray($response);
    }

    /**
     * Creates a new entitlement.
     *
     * @param  CreateEntitlement  $requestDto  Data transfer object containing the details for the new entitlement.
     * @param  string  $projectId  The ID of the project to which the entitlement belongs.
     * @return Entitlement The newly created entitlement.
     */
    public function create(CreateEntitlement $requestDto, string $projectId): Entitlement
    {
        $endpoint = sprintf('/projects/%s/entitlements', $projectId);
        $response = $this->sendRequest('POST', $endpoint, $requestDto->toArray());

        return Entitlement::fromArray($response);
    }

    /**
     * Updates an entitlement with the given ID.
     *
     * @param  string  $entitlementId  The ID of the entitlement to update.
     * @param  UpdateEntitlement  $requestDto  The data transfer object entitlement.
     * @param  string  $projectId  The ID of the project to which the entitlement belongs.
     * @return Entitlement The updated entitlement.
     */
    public function update(string $entitlementId, UpdateEntitlement $requestDto, string $projectId): Entitlement
    {
        $endpoint = sprintf('/projects/%s/entitlements/%s', $projectId, $entitlementId);
        $response = $this->sendRequest('POST', $endpoint, $requestDto->toArray());

        return Entitlement::fromArray($response);
    }

    /**
     * Deletes an entitlement based on the provided entitlement ID and project ID.
     *
     * @param  string  $entitlementId  The ID of the entitlement to be deleted.
     * @param  string  $projectId  The ID of the project associated with the entitlement.
     */
    public function delete(string $entitlementId, string $projectId): void
    {
        $endpoint = sprintf('/projects/%s/entitlements/%s', $projectId, $entitlementId);
        $this->sendRequest('DELETE', $endpoint);
    }

    /**
     * Retrieves a list of products associated with a specific entitlement.
     *
     * @param  string  $entitlementId  The ID of the entitlement.
     * @param  string  $projectId  The ID of the project.
     * @param  int|null  $limit  Optional. The maximum number of products to return.
     * @param  string|null  $startingAfter  Optional. A cursor for pagination
     * @return Paginator<Product> A paginator instance containing the list of products.
     */
    public function getProducts(
        string $entitlementId,
        string $projectId,
        ?int $limit = null,
        ?string $startingAfter = null
    ): Paginator {
        $endpoint = sprintf('/projects/%s/entitlements/%s/products', $projectId, $entitlementId);

        if ($limit !== null || $startingAfter !== null) {
            $endpoint = $this->buildNextPageUrl($endpoint, [
                'limit' => $limit,
                'starting_after' => $startingAfter,
            ]);
        }

        $response = $this->sendRequest('GET', $endpoint);
        $response['items'] = array_map(
            static fn ($item): Product => Product::fromArray($item),
            $response['items'] ?? []
        );

        return new Paginator($response['items'], $response['next_page'], $response['url']);
    }

    /**
     * Attach products to an entitlement.
     *
     * @param  string  $entitlementId  The ID of the entitlement to which the products will be attached.
     * @param  array  $productIds  An array of product IDs to be attached to the entitlement.
     * @param  string  $projectId  The ID of the project to which the entitlement belongs.
     * @return Entitlement The updated entitlement with the attached products.
     */
    public function attachProduct(string $entitlementId, array $productIds, string $projectId): Entitlement
    {
        $endpoint = sprintf('/projects/%s/entitlements/%s/actions/attach_products', $projectId, $entitlementId);
        $response = $this->sendRequest('POST', $endpoint, [
            'product_ids' => $productIds,
        ]);

        return Entitlement::fromArray($response);
    }

    /**
     * Detaches products from a given entitlement.
     *
     * @param  string  $entitlementId  The ID of the entitlement from which products will be detached.
     * @param  array  $productIds  An array of product IDs to be detached from the entitlement.
     * @param  string  $projectId  The ID of the project to which the entitlement belongs.
     * @return Entitlement The updated entitlement after detaching the products.
     */
    public function detachProduct(string $entitlementId, array $productIds, string $projectId): Entitlement
    {
        $endpoint = sprintf('/projects/%s/entitlements/%s/actions/detach_products', $projectId, $entitlementId);
        $response = $this->sendRequest('POST', $endpoint, [
            'product_ids' => $productIds,
        ]);

        return Entitlement::fromArray($response);
    }
}
