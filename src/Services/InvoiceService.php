<?php

declare(strict_types=1);

namespace Twovmodules\RevenueCat\Services;

use Twovmodules\RevenueCat\Dto\Paginator;
use Twovmodules\RevenueCat\Dto\Response\Invoice;
use Twovmodules\RevenueCat\Dto\Response\InvoiceFile;

class InvoiceService extends BaseService
{
    /**
     * Retrieves a list of invoices for a given customer and project.
     *
     * @param  string  $customerId  The ID of the customer whose invoices are to be retrieved.
     * @param  string  $projectId  The ID of the project associated with the invoices.
     * @param  int|null  $limit  Optional. The maximum number of invoices to retrieve.
     * @param  string|null  $startingAfter  Optional. A cursor for pagination.
     * @return Paginator<Invoice> A paginator instance containing the list of invoices.
     */
    public function list(
        string $customerId,
        string $projectId,
        ?int $limit = null,
        ?string $startingAfter = null
    ): Paginator {
        $endpoint = sprintf('/projects/%s/customers/%s/invoices', $projectId, $customerId);

        if ($limit !== null || $startingAfter !== null) {
            $endpoint = $this->buildNextPageUrl($endpoint, [
                'limit' => $limit,
                'starting_after' => $startingAfter,
            ]);
        }

        $response = $this->sendRequest('GET', $endpoint);

        $response['items'] = array_map(static fn ($item): Invoice => Invoice::fromArray($item), $response['items'] ?? []);

        return new Paginator($response['items'], $response['next_page'], $response['url']);
    }

    /**
     * Retrieve an invoice file for a specific customer and project.
     *
     * @param  string  $customerId  The ID of the customer.
     * @param  string  $invoiceId  The ID of the invoice.
     * @param  string  $projectId  The ID of the project.
     * @return InvoiceFile The invoice file.
     */
    public function get(string $customerId, string $invoiceId, string $projectId): InvoiceFile
    {
        $endpoint = sprintf('/projects/%s/customers/%s/invoices/%s/file', $projectId, $customerId, $invoiceId);
        $this->sendRequest('GET', $endpoint);
        $headerLocation = $this->response->getHeaderLine('location');

        return new InvoiceFile($headerLocation);
    }
}
