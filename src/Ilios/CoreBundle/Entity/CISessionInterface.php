<?php

namespace Ilios\CoreBundle\Entity;

use Symfony\Component\DependencyInjection\ContainerAware;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

/**
 * Interface CISessionInterface
 */
interface CISessionInterface extends IdentifiableEntityInterface
{
    public function setIpAddress($ipAddress);

    public function getIpAddress();

    public function setUserAgent($userAgent);

    public function getUserAgent();

    public function setLastActivity($lastActivity);

    public function getLastActivity();

    public function setUserData($userData);

    public function getUserData();

    public function getUserDataItem($key);
}
