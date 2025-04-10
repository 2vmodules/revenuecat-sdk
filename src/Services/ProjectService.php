<?php

declare(strict_types=1);

namespace Twovmodules\RevenueCat\Services;

use Twovmodules\RevenueCat\Dto\Paginator;
use Twovmodules\RevenueCat\Dto\Response\Project;

class ProjectService extends BaseService
{
    /**
     * Retrieves a list of projects with optional pagination.
     *
     * @param  int|null  $limit  The maximum number of projects to return. If null, the default limit is used.
     * @param  string|null  $startingAfter  The cursor for pagination. If null, the first page is returned.
     * @return Paginator<Project> A paginator instance containing the list of projects
     */
    public function list(?int $limit = null, ?string $startingAfter = null): Paginator
    {
        $endpoint = '/projects';

        if ($limit !== null || $startingAfter !== null) {
            $endpoint = $this->buildNextPageUrl($endpoint, [
                'limit' => $limit,
                'starting_after' => $startingAfter,
            ]);
        }

        $response = $this->sendRequest('GET', $endpoint);

        $response['items'] = array_map(static fn ($item): Project => Project::fromArray($item), $response['items'] ?? []);

        return new Paginator($response['items'], $response['next_page'], $response['url']);
    }
}
