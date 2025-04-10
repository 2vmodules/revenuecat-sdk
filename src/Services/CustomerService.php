<?php

declare(strict_types=1);

namespace Twovmodules\RevenueCat\Services;

use Twovmodules\RevenueCat\Dto\Paginator;
use Twovmodules\RevenueCat\Dto\Request\CreateCustomerAttribute;
use Twovmodules\RevenueCat\Dto\Response\Customer;
use Twovmodules\RevenueCat\Dto\Response\CustomerAlias;
use Twovmodules\RevenueCat\Dto\Response\CustomerAttribute;
use Twovmodules\RevenueCat\Dto\Response\CustomerEntitlement;
use Twovmodules\RevenueCat\Dto\Response\Purchase;
use Twovmodules\RevenueCat\Dto\Response\Subscription;
use Twovmodules\RevenueCat\Enum\EnvironmentType;

class CustomerService extends BaseService
{
    /**
     * Retrieves a list of customers for a given project.
     *
     * @param  string  $projectId  The ID of the project.
     * @param  int|null  $limit  Optional. The maximum number of customers to return.
     * @param  string|null  $startingAfter  Optional. A cursor for pagination.
     * @return Paginator<Customer> A paginator object containing the list of customers.
     */
    public function list(string $projectId, ?int $limit = null, ?string $startingAfter = null): Paginator
    {
        $endpoint = sprintf('/projects/%s/customers', $projectId);

        if ($limit !== null || $startingAfter !== null) {
            $endpoint = $this->buildNextPageUrl($endpoint, [
                'limit' => $limit,
                'starting_after' => $startingAfter,
            ]);
        }

        $response = $this->sendRequest('GET', $endpoint);

        $response['items'] = array_map(
            static fn ($customerData): Customer => Customer::fromArray($customerData),
            $response['items'] ?? []
        );

        return new Paginator($response['items'], $response['next_page'], $response['url']);
    }

    /**
     * Retrieves a customer by their ID and project ID.
     *
     * @param  string  $customerId  The ID of the customer to retrieve.
     * @param  string  $projectId  The ID of the project the customer belongs to.
     * @return Customer|null The customer object if found, null otherwise.
     */
    public function get(string $customerId, string $projectId): ?Customer
    {
        $endpoint = sprintf('/projects/%s/customers/%s', $projectId, $customerId);
        $response = $this->sendRequest('GET', $endpoint);

        return $response === [] ? null : Customer::fromArray($response);
    }

    /**
     * Creates a new customer in the specified project.
     *
     * @param  string  $newCustomerId  The ID of the new customer to be created.
     * @param  CreateCustomerAttribute[]|null  $attributes  Optional attributes associated with the new customer.
     * @param  string  $projectId  The ID of the project where the customer will be created.
     * @return Customer The created customer object.
     */
    public function create(string $newCustomerId, ?array $attributes, string $projectId): Customer
    {
        $endpoint = sprintf('/projects/%s/customers', $projectId);

        $customer = [
            'id' => $newCustomerId,
        ];

        if ($attributes !== null) {
            $customer['attributes'] = $attributes;
        }

        $response = $this->sendRequest('POST', $endpoint, $customer);

        return Customer::fromArray($response);
    }

    /**
     * Deletes a customer from a project.
     *
     * @param  string  $customerId  The ID of the customer to delete.
     * @param  string  $projectId  The ID of the project from which the customer will be deleted.
     */
    public function delete(string $customerId, string $projectId): void
    {
        $endpoint = sprintf('/projects/%s/customers/%s', $projectId, $customerId);
        $this->sendRequest('DELETE', $endpoint);
    }

    /**
     * Retrieves the subscriptions for a given customer.
     *
     * @param  string  $customerId  The ID of the customer.
     * @param  string  $projectId  The ID of the project.
     * @param  int|null  $limit  The maximum number of subscriptions to retrieve (optional).
     * @param  string|null  $startingAfter  The cursor for pagination.
     * @param  EnvironmentType|null  $environmentType  The environment type (e.g., production, sandbox).
     * @return Paginator<Subscription> A paginator instance containing the subscriptions.
     */
    public function getSubscriptions(
        string $customerId,
        string $projectId,
        ?int $limit = null,
        ?string $startingAfter = null,
        ?EnvironmentType $environmentType = null
    ): Paginator {
        $endpoint = sprintf('/projects/%s/customers/%s/subscriptions?expand=products', $projectId, $customerId);

        if ($limit !== null || $startingAfter !== null || $environmentType instanceof EnvironmentType) {
            $endpoint = $this->buildNextPageUrl($endpoint, [
                'limit' => $limit,
                'starting_after' => $startingAfter,
                'environment' => $environmentType?->value,
            ]);
        }

        $response = $this->sendRequest('GET', $endpoint);

        $response['items'] = array_map(
            static fn ($subscriptionData): Subscription => Subscription::fromArray($subscriptionData),
            $response['items'] ?? []
        );

        return new Paginator($response['items'], $response['next_page'], $response['url']);
    }

    /**
     * Retrieves the purchases for a given customer.
     *
     * @param  string  $customerId  The ID of the customer.
     * @param  string  $projectId  The ID of the project.
     * @param  int|null  $limit  The maximum number of purchases to retrieve (optional).
     * @param  string|null  $startingAfter  The cursor for pagination, indicating the starting point (optional).
     * @return Paginator<Purchase> A paginator instance or an array of purchases.
     */
    public function getPurchases(
        string $customerId,
        string $projectId,
        ?int $limit = null,
        ?string $startingAfter = null
    ): Paginator {
        $endpoint = sprintf('/projects/%s/customers/%s/purchases', $projectId, $customerId);

        if ($limit !== null || $startingAfter !== null) {
            $endpoint = $this->buildNextPageUrl($endpoint, [
                'limit' => $limit,
                'starting_after' => $startingAfter,
            ]);
        }

        $response = $this->sendRequest('GET', $endpoint);

        $response['items'] = array_map(
            static fn ($purchaseData): Purchase => Purchase::fromArray($purchaseData),
            $response['items'] ?? []
        );

        return new Paginator($response['items'], $response['next_page'], $response['url']);
    }

    /**
     * Retrieve the entitlements for a specific customer.
     *
     * @param  string  $customerId  The ID of the customer.
     * @param  string  $projectId  The ID of the project.
     * @param  int|null  $limit  Optional. The maximum number of entitlements to retrieve.
     * @param  string|null  $startingAfter  Optional. A cursor for pagination, indicating the starting point.
     * @return Paginator<CustomerEntitlement> A paginator instance containing the entitlements.
     */
    public function getEntitlements(
        string $customerId,
        string $projectId,
        ?int $limit = null,
        ?string $startingAfter = null
    ): Paginator {
        $endpoint = sprintf('/projects/%s/customers/%s/active_entitlements', $projectId, $customerId);

        if ($limit !== null || $startingAfter !== null) {
            $endpoint = $this->buildNextPageUrl($endpoint, [
                'limit' => $limit,
                'starting_after' => $startingAfter,
            ]);
        }

        $response = $this->sendRequest('GET', $endpoint);

        $response['items'] = array_map(
            static fn ($entitlementData): CustomerEntitlement => CustomerEntitlement::fromArray($entitlementData),
            $response['items'] ?? []
        );

        return new Paginator($response['items'], $response['next_page'], $response['url']);
    }

    /**
     * Retrieves the aliases associated with a given customer ID and project ID.
     *
     * @param  string  $customerId  The ID of the customer whose aliases are to be retrieved.
     * @param  string  $projectId  The ID of the project associated with the customer.
     * @return CustomerAlias[] An array of aliases associated with the specified customer and project.
     */
    public function getAliases(string $customerId, string $projectId): array
    {
        $endpoint = sprintf('/projects/%s/customers/%s/aliases', $projectId, $customerId);
        $response = $this->sendRequest('GET', $endpoint);

        return array_map(
            static fn ($aliasData): CustomerAlias => CustomerAlias::fromArray($aliasData),
            $response['items'] ?? []
        );
    }

    /**
     * Retrieves the attributes of a customer.
     *
     * @param  string  $customerId  The ID of the customer.
     * @param  string  $projectId  The ID of the project.
     * @param  int|null  $limit  Optional. The maximum number of attributes to retrieve.
     * @param  string|null  $startingAfter  Optional. A cursor for pagination, indicating the starting point.
     * @return Paginator<CustomerAttribute> A paginator instance containing the customer's attributes.
     */
    public function getAttributes(
        string $customerId,
        string $projectId,
        ?int $limit = null,
        ?string $startingAfter = null
    ): Paginator {
        $endpoint = sprintf('/projects/%s/customers/%s/attributes', $projectId, $customerId);

        if ($limit !== null || $startingAfter !== null) {
            $endpoint = $this->buildNextPageUrl($endpoint, [
                'limit' => $limit,
                'starting_after' => $startingAfter,
            ]);
        }

        $response = $this->sendRequest('GET', $endpoint);

        $response['items'] = array_map(
            static fn ($attributesData): CustomerAttribute => CustomerAttribute::fromArray($attributesData),
            $response['items'] ?? []
        );

        return new Paginator($response['items'], $response['next_page'], $response['url']);
    }

    /**
     * Creates a new attribute for a customer.
     *
     * @param  CreateCustomerAttribute[]  $attributes  The data for the new attribute.
     * @param  string  $customerId  The ID of the customer.
     * @param  string  $projectId  The ID of the project.
     * @return Paginator<CustomerAttribute> The paginator instance containing the created attribute.
     */
    public function createAttributes(array $attributes, string $customerId, string $projectId): Paginator
    {
        $endpoint = sprintf('/projects/%s/customers/%s/attributes', $projectId, $customerId);

        $response = $this->sendRequest('POST', $endpoint, [
                'attributes' => $attributes,
            ]);

        $response['items'] = array_map(
            static fn ($attributesData): CustomerAttribute => CustomerAttribute::fromArray($attributesData),
            $response['items'] ?? []
        );

        return new Paginator($response['items'], $response['next_page'], $response['url']);
    }
}
