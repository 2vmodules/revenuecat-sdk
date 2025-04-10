<?php

declare(strict_types=1);

namespace Twovmodules\RevenueCat\Services;

use Twovmodules\RevenueCat\Dto\Response\OverviewMetrics;

class OverviewMetricsService extends BaseService
{
    /**
     * Retrieves the overview metrics for a given project.
     *
     * @param  string  $projectId  The ID of the project to retrieve metrics for.
     * @return OverviewMetrics[]|null The overview metrics for the project, or null if not found.
     */
    public function get(string $projectId): ?array
    {
        $endpoint = sprintf('/projects/%s/metrics/overview', $projectId);
        $response = $this->sendRequest('GET', $endpoint);

        $items = array_map(
            static fn ($item): OverviewMetrics => OverviewMetrics::fromArray($item),
            $response['metrics'] ?? []
        );

        return $items === [] ? null : $items;
    }
}
