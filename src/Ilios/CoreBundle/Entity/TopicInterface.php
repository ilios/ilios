<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;
use Ilios\CoreBundle\Traits\CoursesEntityInterface;
use Ilios\CoreBundle\Traits\SessionsEntityInterface;
use Ilios\CoreBundle\Traits\ProgramYearsEntityInterface;
use Ilios\CoreBundle\Traits\SchoolEntityInterface;

/**
 * Interface TopicInterface
 *
 * @deprecated 
 */
interface TopicInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    CoursesEntityInterface,
    SessionsEntityInterface,
    ProgramYearsEntityInterface,
    SchoolEntityInterface,
    LoggableEntityInterface
{
}
