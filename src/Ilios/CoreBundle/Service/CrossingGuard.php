<?php

namespace Ilios\CoreBundle\Service;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class CrossingGuard
{
    const GUARD = 'crossing-guard-enabled.lock';

    protected $fs;

    /**
     * CrossingGuard constructor.
     *
     * @param IliosFileSystem $fs
     */
    public function __construct(IliosFileSystem $fs)
    {
        $this->fs = $fs;
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
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        while ($this->isStopped()) {
            sleep(1);
        }

        return;
    }
}
