<?php

declare(strict_types=1);

namespace Twovmodules\RevenueCat\Services;

use Twovmodules\RevenueCat\Dto\Paginator;
use Twovmodules\RevenueCat\Dto\Response\Entitlement;
use Twovmodules\RevenueCat\Dto\Response\Purchase;

class PurchaseService extends BaseService
{
    /**
     * Retrieves a list of purchases.
     *
     * @param  string  $purchaseId  The ID of the purchase.
     * @param  string  $projectId  The ID of the project.
     * @param  int|null  $limit  The maximum number of purchases to return. Optional.
     * @param  string|null  $startingAfter  A cursor for pagination. Optional.
     * @return Paginator<Entitlement> A paginator instance containing the list of purchases.
     */
    public function list(
        string $purchaseId,
        string $projectId,
        ?int $limit = null,
        ?string $startingAfter = null
    ): Paginator {
        $endpoint = sprintf('/projects/%s/purchases/%s/entitlements', $projectId, $purchaseId);

        if ($limit !== null || $startingAfter !== null) {
            $endpoint = $this->buildNextPageUrl($endpoint, [
                'limit' => $limit,
                'starting_after' => $startingAfter,
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
     * Retrieves a purchase by its ID and project ID.
     *
     * @param  string  $purchaseId  The ID of the purchase to retrieve.
     * @param  string  $projectId  The ID of the project associated with the purchase.
     * @return Purchase The purchase details.
     */
    public function get(string $purchaseId, string $projectId): Purchase
    {
        $endpoint = sprintf('/projects/%sPaginator/%s', $projectId, $purchaseId);
        $response = $this->sendRequest('GET', $endpoint);

        return Purchase::fromArray($response);
    }

    /**
     * Refunds a purchase.
     *
     * @param  string  $purchaseId  The ID of the purchase to be refunded.
     * @param  string  $projectId  The ID of the project associated with the purchase.
     * @return Purchase The refunded purchase object.
     */
    public function refund(string $purchaseId, string $projectId): Purchase
    {
        $endpoint = sprintf('/projects/%s/%s/actions/refund', $projectId, $purchaseId);
        $response = $this->sendRequest('POST', $endpoint);

        return Purchase::fromArray($response);
    }
}
