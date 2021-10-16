<?php

declare(strict_types=1);

namespace App\Service;

use Sentry\Event;
use Shivas\VersioningBundle\Service\VersionManagerInterface;

class SentryBeforeSend
{
    protected bool $errorCaptureEnabled;

    public function __construct(
        protected VersionManagerInterface $versionManager,
        Config $config,
    ) {
        $this->errorCaptureEnabled = (bool) $config->get('errorCaptureEnabled');
    }

    public function __invoke(Event $event)
    {
        if (!$this->errorCaptureEnabled) {
            return null;
        }
        $event->setRelease($this->versionManager->getVersion()->toString());

        return $event;
    }
}
