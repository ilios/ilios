<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface AamcMethodInterface
 */
interface AamcMethodInterface 
{
    public function setMethodId($methodId);

    public function getMethodId();

    public function setDescription($description);

    public function getDescription();

    public function addSessionType(\Ilios\CoreBundle\Model\SessionType $sessionTypes);

    public function removeSessionType(\Ilios\CoreBundle\Model\SessionType $sessionTypes);

    public function getSessionTypes();
}

