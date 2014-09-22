<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface IngestionExceptionInterface
 */
interface IngestionExceptionInterface 
{
    public function setUserId($userId);

    public function getUserId();

    public function setIngestedWideUid($ingestedWideUid);

    public function getIngestedWideUid();

    public function setUser(\Ilios\CoreBundle\Model\User $user = null);

    public function getUser();
}
