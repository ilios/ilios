<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface SessionDescriptionInterface
 */
interface SessionDescriptionInterface 
{
    public function setSessionId($sessionId);

    public function getSessionId();

    public function setDescription($description);

    public function getDescription();

    public function setSession(\Ilios\CoreBundle\Model\Session $session = null);

    public function getSession();
}
