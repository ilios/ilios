<?php

declare(strict_types=1);

namespace App\Tests\Command\TestDoubles;

use App\Command\UpdateFrontendCommand;

class TestableUpdateFrontendCommand extends UpdateFrontendCommand
{
    private ?string $currentVersion = null;
    private array $distributions = [];

    public function setTestData(?string $currentVersion, array $distributions): void
    {
        $this->currentVersion = $currentVersion;
        $this->distributions = $distributions;
    }

    protected function downloadAndExtractAllArchives(string $environment): ?string
    {
        return $this->currentVersion;
    }

    protected function listDistributions(string $environment): array
    {
        return $this->distributions;
    }

    protected function copyAssetsIntoPublicDirectory(string $distributionPath): void
    {
        // no-op
    }

    protected function activateVersion(string $distributionPath): void
    {
        // no-op
    }
}
