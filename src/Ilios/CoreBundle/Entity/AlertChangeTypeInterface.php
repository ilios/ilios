<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\AlertableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;

/**
 * Interface AlertChangeTypeInterface
 */
interface AlertChangeTypeInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    StringableEntityInterface,
    LoggableEntityInterface,
    AlertableEntityInterface
{
    /**
     * Indicates a course director change.
     * @var int
     */
    const CHANGE_TYPE_COURSE_DIRECTOR = 5;
    /**
     * Indicates a course instructor change.
     * @var int
     */
    const CHANGE_TYPE_INSTRUCTOR = 4;
    /**
     * Indicates a learning material change.
     * @var int
     */
    const CHANGE_TYPE_LEARNING_MATERIAL = 3;
    /**
     * Indicates a learner group change.
     * @var int
     */
    const CHANGE_TYPE_LEARNER_GROUP = 6;
    /**
     * Indicates a location change.
     * @var int
     */
    const CHANGE_TYPE_LOCATION = 2;
    /**
     * Indicates the addition of a new offering.
     * @var int
     */
    const CHANGE_TYPE_NEW_OFFERING = 7;
    /**
     * Indicates a session's publication status change.
     * @var int
     */
    const CHANGE_TYPE_SESSION_PUBLISH = 8;
    /**
     * Indicates a time change.
     * @var int
     */
    const CHANGE_TYPE_TIME = 1;
}
