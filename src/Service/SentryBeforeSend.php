<?php

declare(strict_types=1);

namespace App\Service;

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

        // @todo read Ilios version from package file and set it here [ST 2025/02/23]
        // $event->setRelease($this->versionManager->getVersion()->toString());

        return $event;
    }
}
