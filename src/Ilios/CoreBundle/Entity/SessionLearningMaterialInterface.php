<?php

namespace Ilios\CoreBundle\Entity;

/**
 * Interface SessionLearningMaterialInterface
 */
interface SessionLearningMaterialInterface extends LearningMaterialRelationshipInterface, SessionStampableInterface
{
    /**
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session);

    /**
     * @return SessionInterface|null
     */
    public function getSession();
}
