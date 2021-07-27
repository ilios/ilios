<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class CrossingGuard
{
    public const GUARD = 'crossing-guard-enabled.lock';

    /**
     * CrossingGuard constructor.
     */
    public function __construct(protected IliosFileSystem $fs)
    {
    }

    /**
     * Check if the crossing guard is down
     * @return bool
     */
    public function isStopped()
    {
        return $this->fs->hasLock(self::GUARD);
    }

    public function enable()
    {
        $this->fs->createLock(self::GUARD);
    }

    public function disable()
    {
        $this->fs->releaseLock(self::GUARD);
    }

    /**
     * Listen to all requests and, if they are stopped, wait
     */
    public function onKernelRequest(RequestEvent $event)
    {
        while ($this->isStopped()) {
            sleep(1);
        }
    }
}
