<?php

declare(strict_types=1);

namespace Twovmodules\RevenueCat\Services;

use Twovmodules\RevenueCat\Dto\Paginator;
use Twovmodules\RevenueCat\Dto\Request\CreateApp;
use Twovmodules\RevenueCat\Dto\Request\UpdateApp;
use Twovmodules\RevenueCat\Dto\Response\App;

/**
 * Service for managing RevenueCat Apps
 */
class AppService extends BaseService
{
    /**
     * Retrieves a list of apps for a given project.
     *
     * @param  string  $projectId  The ID of the project.
     * @param  int|null  $limit  Optional. The maximum number of items to return.
     * @param  string|null  $startingAfter  Optional. A cursor for pagination, indicating the starting point.
     * @return Paginator<App> A paginator instance containing the list of apps.
     */
    public function list(string $projectId, ?int $limit = null, ?string $startingAfter = null): Paginator
    {
        $endpoint = sprintf('/projects/%s/apps', $projectId);

        if ($limit !== null || $startingAfter !== null) {
            $endpoint = $this->buildNextPageUrl($endpoint, [
                'limit' => $limit,
                'starting_after' => $startingAfter,
            ]);
        }

        $response = $this->sendRequest('GET', $endpoint);

        $response['items'] = array_map(static fn ($item): App => App::fromArray($item), $response['items'] ?? []);

        return new Paginator($response['items'], $response['next_page'], $response['url']);
    }

    /**
     * Retrieve a specific app by its ID
     *
     * @param  string  $appId  Unique identifier of the app
     * @param  string  $projectId  The ID of the project.
     * @return App Detailed app information
     */
    public function get(string $appId, string $projectId): App
    {
        $endpoint = sprintf('/projects/%s/apps/%s', $projectId, $appId);

        $response = $this->sendRequest('GET', $endpoint);

        return App::fromArray($response);
    }

    /**
     * Create a new app in the specified project
     *
     * @param  CreateApp  $requestDto  Data transfer object with app creation details
     * @param  string  $projectId  The ID of the project.
     * @return App Newly created app details
     */
    public function create(CreateApp $requestDto, string $projectId): App
    {
        $endpoint = sprintf('/projects/%s/apps', $projectId);
        $response = $this->sendRequest('POST', $endpoint, $requestDto->toArray());

        return App::fromArray($response);
    }

    /**
     * Update an existing app
     *
     * @param  string  $appId  Unique identifier of the app to update
     * @param  UpdateApp  $requestDto  Data transfer object with app update details
     * @param  string  $projectId  The ID of the project.
     * @return App Updated app details
     */
    public function update(string $appId, UpdateApp $requestDto, string $projectId): App
    {
        $endpoint = sprintf('/projects/%s/apps/%s', $projectId, $appId);
        $response = $this->sendRequest('POST', $endpoint, $requestDto->toArray());

        return App::fromArray($response);
    }

    /**
     * Deletes an app with the given app ID from the specified project.
     *
     * If the project ID is not provided, it will use the default project ID from the configuration.
     *
     * @param  string  $appId  The ID of the app to be deleted.
     * @param  string  $projectId  The ID of the project.
     */
    public function delete(string $appId, string $projectId): void
    {
        $endpoint = sprintf('/projects/%s/apps/%s', $projectId, $appId);
        $this->sendRequest('DELETE', $endpoint);
    }
}
