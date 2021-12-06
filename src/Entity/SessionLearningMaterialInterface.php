<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IndexableCoursesEntityInterface;

/**
 * Interface SessionLearningMaterialInterface
 */
interface SessionLearningMaterialInterface extends
    LearningMaterialRelationshipInterface,
    SessionStampableInterface,
    IndexableCoursesEntityInterface
{
    public function setSession(SessionInterface $session);

    /**
     * @return SessionInterface|null
     */
    public function getSession(): ?SessionInterface;
}
