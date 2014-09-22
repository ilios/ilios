<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface ApiKeyInterface
 */
interface ApiKeyInterface 
{
    public function setUserId($userId);

    public function getUserId();

    public function setApiKey($apiKey);

    public function getApiKey();

    public function setUser(\Ilios\CoreBundle\Model\User $user = null);

    public function getUser();
}

