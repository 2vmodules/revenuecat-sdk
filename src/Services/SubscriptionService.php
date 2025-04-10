<?php

declare(strict_types=1);

namespace Twovmodules\RevenueCat\Services;

use Twovmodules\RevenueCat\Dto\Paginator;
use Twovmodules\RevenueCat\Dto\Response\Entitlement;
use Twovmodules\RevenueCat\Dto\Response\Subscription;

class SubscriptionService extends BaseService
{
    /**
     * Retrieves a list of subscriptions.
     *
     * @param  string  $subscriptionId  The ID of the subscription to list.
     * @param  string  $projectId  The ID of the project associated with the subscription.
     * @param  int|null  $limit  Optional. The maximum number of subscriptions to return.
     * @param  string|null  $startingAfter  Optional. A cursor for pagination
     * @return Paginator<Entitlement> A paginator instance containing the list of subscriptions.
     */
    public function list(
        string $subscriptionId,
        string $projectId,
        ?int $limit = null,
        ?string $startingAfter = null
    ): Paginator {
        $endpoint = sprintf('/projects/%s/subscriptions/%s/entitlements', $projectId, $subscriptionId);

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
     * Retrieve a subscription by its ID and project ID.
     *
     * @param  string  $subscriptionId  The ID of the subscription to retrieve.
     * @param  string  $projectId  The ID of the project associated with the subscription.
     * @return Subscription The subscription object.
     */
    public function get(string $subscriptionId, string $projectId): Subscription
    {
        $endpoint = sprintf('/projects/%s/subscriptions/%s', $projectId, $subscriptionId);
        $response = $this->sendRequest('GET', $endpoint);

        return Subscription::fromArray($response);
    }

    /**
     * Cancels a subscription.
     *
     * @param  string  $subscriptionId  The ID of the subscription to cancel.
     * @param  string  $projectId  The ID of the project associated with the subscription.
     * @return Subscription The canceled subscription.
     */
    public function cancel(string $subscriptionId, string $projectId): Subscription
    {
        $endpoint = sprintf('/projects/%s/subscriptions/%s/actions/cancel', $projectId, $subscriptionId);
        $response = $this->sendRequest('POST', $endpoint);

        return Subscription::fromArray($response);
    }

    /**
     * Refunds a subscription for a given subscription ID and project ID.
     *
     * @param  string  $subscriptionId  The ID of the subscription to be refunded.
     * @param  string  $projectId  The ID of the project associated with the subscription.
     * @return Subscription The refunded subscription object.
     */
    public function refund(string $subscriptionId, string $projectId): Subscription
    {
        $endpoint = sprintf('/projects/%s/subscriptions/%s/actions/refund', $projectId, $subscriptionId);
        $response = $this->sendRequest('POST', $endpoint);

        return Subscription::fromArray($response);
    }
}
