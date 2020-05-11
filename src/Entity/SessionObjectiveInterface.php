<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IndexableCoursesEntityInterface;
use App\Traits\ObjectiveRelationshipInterface;

/**
 * Interface SessionObjectiveInterface
 */
interface SessionObjectiveInterface extends
    IndexableCoursesEntityInterface,
    ObjectiveRelationshipInterface,
    SessionStampableInterface
{
    /**
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session): void;

    /**
     * @return SessionInterface
     */
    public function getSession(): SessionInterface;
}
