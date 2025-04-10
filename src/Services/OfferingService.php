<?php

declare(strict_types=1);

namespace Twovmodules\RevenueCat\Services;

use Twovmodules\RevenueCat\Dto\Paginator;
use Twovmodules\RevenueCat\Dto\Request\CreateOffering;
use Twovmodules\RevenueCat\Dto\Request\UpdateOffering;
use Twovmodules\RevenueCat\Dto\Response\Offering;

class OfferingService extends BaseService
{
    /**
     * Retrieves a list of offerings for a given project.
     *
     * @param  string  $projectId  The ID of the project for which to list offerings.
     * @param  bool|null  $expand  Optional. Whether to expand the offerings with additional details.
     * @param  int|null  $limit  Optional. The maximum number of offerings to return.
     * @param  string|null  $startingAfter  Optional. A cursor for pagination.
     * @return Paginator<Offering> A paginator instance containing the list of offerings.
     */
    public function list(
        string $projectId,
        ?bool $expand = null,
        ?int $limit = null,
        ?string $startingAfter = null
    ): Paginator {
        $endpoint = sprintf('/projects/%s/offerings', $projectId);

        if ($limit !== null || $startingAfter !== null || $expand !== null) {
            $endpoint = $this->buildNextPageUrl($endpoint, [
                'limit' => $limit,
                'starting_after' => $startingAfter,
                'expand' => 'items.package.product',
            ]);
        }

        $response = $this->sendRequest('GET', $endpoint);

        $response['items'] = array_map(
            static fn ($item): Offering => Offering::fromArray($item),
            $response['items'] ?? []
        );

        return new Paginator($response['items'], $response['next_page'], $response['url']);
    }

    /**
     * Retrieves an offering by its ID and project ID.
     *
     * @param  string  $offeringId  The ID of the offering to retrieve.
     * @param  string  $projectId  The ID of the project associated with the offering.
     * @param  bool|null  $expand  Optional parameter to expand additional offering details.
     * @return Offering The offering object corresponding to the provided IDs.
     */
    public function get(string $offeringId, string $projectId, ?bool $expand = null): Offering
    {
        $endpoint = sprintf('/projects/%s/offerings/%s', $projectId, $offeringId);

        if ($expand !== null) {
            $endpoint = $this->buildNextPageUrl($endpoint, [
                'expand' => 'package.product',
            ]);
        }

        $response = $this->sendRequest('GET', $endpoint);

        return Offering::fromArray($response);
    }

    /**
     * Creates a new offering.
     *
     * @param  CreateOffering  $requestDto  Data transfer object of the offering to be created.
     * @param  string  $projectId  The ID of the project to which the offering belongs.
     * @return Offering The created offering.
     */
    public function create(CreateOffering $requestDto, string $projectId): Offering
    {
        $endpoint = sprintf('/projects/%s/offerings', $projectId);
        $response = $this->sendRequest('POST', $endpoint, $requestDto->toArray());

        return Offering::fromArray($response);
    }

    /**
     * Updates an offering with the given offering ID.
     *
     * @param  string  $offeringId  The ID of the offering to update.
     * @param  UpdateOffering  $requestDto  The data transfer object containing the update details.
     * @param  string  $projectId  The ID of the project to which the offering belongs.
     * @return Offering The updated offering.
     */
    public function update(string $offeringId, UpdateOffering $requestDto, string $projectId): Offering
    {
        $endpoint = sprintf('/projects/%s/offerings/%s', $projectId, $offeringId);
        $response = $this->sendRequest('POST', $endpoint, $requestDto->toArray());

        return Offering::fromArray($response);
    }

    /**
     * Deletes an offering by its ID for a specific project.
     *
     * @param  string  $offeringId  The ID of the offering to be deleted.
     * @param  string  $projectId  The ID of the project associated with the offering.
     */
    public function delete(string $offeringId, string $projectId): void
    {
        $endpoint = sprintf('/projects/%s/offerings/%s', $projectId, $offeringId);
        $this->sendRequest('DELETE', $endpoint);
    }
}
