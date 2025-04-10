<?php

declare(strict_types=1);

namespace Twovmodules\RevenueCat\Services;

use Twovmodules\RevenueCat\Dto\Paginator;
use Twovmodules\RevenueCat\Dto\Request\CreatePackage;
use Twovmodules\RevenueCat\Dto\Request\UpdatePackage;
use Twovmodules\RevenueCat\Dto\Response\Package;
use Twovmodules\RevenueCat\Dto\Response\PackageProduct;

class PackageService extends BaseService
{
    /**
     * Lists the packages for a given offering.
     *
     * @param  string  $offeringId  The ID of the offering to list packages for.
     * @param  bool|null  $expand  Whether to expand the response with additional details.
     * @param  string  $projectId  The ID of the project to filter packages by.
     * @param  int|null  $limit  The maximum number of packages to return.
     * @param  string|null  $startingAfter  A cursor for pagination, indicating the starting point.
     * @return Paginator<Package> A paginator instance containing the list of packages.
     */
    public function list(
        string $offeringId,
        string $projectId,
        ?bool $expand = null,
        ?int $limit = null,
        ?string $startingAfter = null
    ): Paginator {
        $endpoint = sprintf('/projects/%s/offerings/%s/packages', $projectId, $offeringId);

        if ($limit !== null || $startingAfter !== null || $expand !== null) {
            $endpoint = $this->buildNextPageUrl($endpoint, [
                'limit' => $limit,
                'starting_after' => $startingAfter,
                'expand' => $expand ? 'items.product' : null,
            ]);
        }

        $response = $this->sendRequest('GET', $endpoint);
        $response['items'] = array_map(
            static fn ($item): Package => Package::fromArray($item),
            $response['items'] ?? []
        );

        return new Paginator($response['items'], $response['next_page'], $response['url']);
    }

    /**
     * Retrieves a package by its ID.
     *
     * @param  string  $packageId  The ID of the package to retrieve.
     * @param  string  $projectId  The ID of the project to which the package belongs. Default is null.
     * @param  bool|null  $expand  Optional. Whether to expand the package details. Default is false.
     * @return Package The package corresponding to the given ID.
     */
    public function get(string $packageId, string $projectId, ?bool $expand = false): Package
    {
        $endpoint = sprintf('/projects/%s/packages/%s', $projectId, $packageId);

        if ($expand) {
            $endpoint = $this->buildNextPageUrl($endpoint, [
                'expand' => 'product',
            ]);
        }

        $response = $this->sendRequest('GET', $endpoint);

        return Package::fromArray($response);
    }

    /**
     * Creates a new package for the specified offering.
     *
     * @param  string  $offeringId  The ID of the offering to which the package belongs.
     * @param  CreatePackage  $requestDto  The data transfer object containing the package details.
     * @param  string  $projectId  The ID of the project.
     * @return Package The created package.
     */
    public function create(string $offeringId, CreatePackage $requestDto, string $projectId): Package
    {
        $endpoint = sprintf('/projects/%s/offerings/%s/packages', $projectId, $offeringId);
        $response = $this->sendRequest('POST', $endpoint, $requestDto->toArray());

        return Package::fromArray($response);
    }

    /**
     * Updates the package with the given package ID.
     *
     * @param  string  $packageId  The ID of the package to update.
     * @param  UpdatePackage  $requestDto  The data transfer object containing the update details.
     * @param  string  $projectId  The ID of the project to which the package belongs.
     * @return Package The updated package.
     */
    public function update(string $packageId, UpdatePackage $requestDto, string $projectId): Package
    {
        $endpoint = sprintf('/projects/%s/packages/%s', $projectId, $packageId);
        $response = $this->sendRequest('POST', $endpoint, $requestDto->toArray());

        return Package::fromArray($response);
    }

    /**
     * Deletes a package by its ID within a specified project.
     *
     * @param  string  $packageId  The ID of the package to delete.
     * @param  string  $projectId  The ID of the project containing the package.
     */
    public function delete(string $packageId, string $projectId): void
    {
        $endpoint = sprintf('/projects/%s/packages/%s', $projectId, $packageId);
        $this->sendRequest('DELETE', $endpoint);
    }

    /**
     * Retrieves a paginated list of products for a given package and project.
     *
     * @param  string  $packageId  The ID of the package.
     * @param  string  $projectId  The ID of the project.
     * @param  int|null  $limit  Optional. The maximum number of products to return.
     * @param  string|null  $startingAfter  Optional. The cursor for pagination, indicating the starting point.
     * @return Paginator<PackageProduct> A paginator instance containing the list of products.
     */
    public function getProducts(
        string $packageId,
        string $projectId,
        ?int $limit = null,
        ?string $startingAfter = null
    ): Paginator {
        $endpoint = sprintf('/projects/%s/packages/%s/products', $projectId, $packageId);

        if ($limit !== null || $startingAfter !== null) {
            $endpoint = $this->buildNextPageUrl($endpoint, [
                'limit' => $limit,
                'starting_after' => $startingAfter,
            ]);
        }

        $response = $this->sendRequest('GET', $endpoint);

        $response['items'] = array_map(
            static fn ($item): PackageProduct => PackageProduct::fromArray($item),
            $response['items'] ?? []
        );

        return new Paginator($response['items'], $response['next_page'], $response['url']);
    }

    public function attachProduct(string $packageId, array $products, string $projectId): Package
    {
        $endpoint = sprintf('/projects/%s/packages/%s/actions/attach_products', $projectId, $packageId);
        $response = $this->sendRequest('POST', $endpoint, [
            'products' => $products,
        ]);

        return Package::fromArray($response);
    }

    public function detachProduct(string $packageId, array $productIds, string $projectId): Package
    {
        $endpoint = sprintf('/projects/%s/packages/%s/actions/detach_products', $projectId, $packageId);
        $response = $this->sendRequest('POST', $endpoint, [
            'product_ids' => $productIds,
        ]);

        return Package::fromArray($response);
    }
}
