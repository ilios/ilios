<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

/**
 * Trait LegacyObjectiveTestTrait
 * @package App\Tests\Endpoints
 */
trait LegacyObjectiveTestTrait
{
    /**
     * @param int $xObjectiveId
     * @param string $filterKey
     * @return array
     */
    protected function getObjectiveForXObjective(int $xObjectiveId, string $filterKey): array
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_objectives_getall",
                [
                    'version' => 'v1',
                    "filters[${filterKey}]" => $xObjectiveId
                ]
            ),
            null,
            $this->getTokenForUser($this->kernelBrowser, 2)
        );

        return json_decode($this->kernelBrowser->getResponse()->getContent(), true)['objectives'][0];
    }
}
