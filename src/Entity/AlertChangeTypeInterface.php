<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\AlertableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\TitledEntityInterface;

interface AlertChangeTypeInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    LoggableEntityInterface,
    AlertableEntityInterface
{
    /**
     * Indicates a course director change.
     */
    public const int CHANGE_TYPE_COURSE_DIRECTOR = 5;
    /**
     * Indicates a course instructor change.
     */
    public const int CHANGE_TYPE_INSTRUCTOR = 4;
    /**
     * Indicates a learning material change.
     */
    public const int CHANGE_TYPE_LEARNING_MATERIAL = 3;
    /**
     * Indicates a learner group change.
     */
    public const int CHANGE_TYPE_LEARNER_GROUP = 6;
    /**
     * Indicates a location change.
     */
    public const int CHANGE_TYPE_LOCATION = 2;
    /**
     * Indicates the addition of a new offering.
     */
    public const int CHANGE_TYPE_NEW_OFFERING = 7;
    /**
     * Indicates a session's publication status change.
     */
    public const int CHANGE_TYPE_SESSION_PUBLISH = 8;
    /**
     * Indicates a time change.
     */
    public const int CHANGE_TYPE_TIME = 1;
}
