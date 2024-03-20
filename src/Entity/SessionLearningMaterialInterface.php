<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IndexableCoursesEntityInterface;

interface SessionLearningMaterialInterface extends
    LearningMaterialRelationshipInterface,
    SessionStampableInterface,
    IndexableCoursesEntityInterface
{
    public function setSession(SessionInterface $session): void;
    public function getSession(): SessionInterface;
}
