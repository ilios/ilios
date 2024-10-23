<?php

declare(strict_types=1);

namespace App\Service;

use Composer\InstalledVersions;
use Sentry\Event;

class SentryBeforeSend
{
    protected bool $errorCaptureEnabled;
    protected ?string $errorCaptureEnvironment;

    public function __construct(
        Config $config,
    ) {
        $this->errorCaptureEnabled = (bool) $config->get('errorCaptureEnabled');
        $this->errorCaptureEnvironment = $config->get('errorCaptureEnvironment');
    }

    public function __invoke(Event $event): ?Event
    {
        if (!$this->errorCaptureEnabled) {
            return null;
        }

        $event->setRelease(InstalledVersions::getPrettyVersion(InstalledVersions::getRootPackage()['name']));
        $event->setEnvironment($this->errorCaptureEnvironment);

        return $event;
    }
}
