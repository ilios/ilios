<?php

declare(strict_types=1);

namespace App\Service;

use Composer\InstalledVersions;
use Sentry\Event;

class SentryBeforeSend
{
    protected bool $errorCaptureEnabled;

    public function __construct(
        Config $config,
    ) {
        $this->errorCaptureEnabled = (bool) $config->get('errorCaptureEnabled');
    }

    public function __invoke(Event $event)
    {
        if (!$this->errorCaptureEnabled) {
            return null;
        }

        $event->setRelease(InstalledVersions::getPrettyVersion(InstalledVersions::getRootPackage()['name']));

        return $event;
    }
}
