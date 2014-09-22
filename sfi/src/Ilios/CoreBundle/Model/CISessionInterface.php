<?php

namespace Ilios\CoreBundle\Model;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Interface CISessionInterface
 */
interface CISessionInterface 
{
    public function setSessionId($sessionId);

    public function getSessionId();

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
