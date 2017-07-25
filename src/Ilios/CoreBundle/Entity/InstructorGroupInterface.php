<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\IlmSessionsEntityInterface;
use Ilios\CoreBundle\Traits\LearnerGroupsEntityInterface;
use Ilios\CoreBundle\Traits\SchoolEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;
use Ilios\CoreBundle\Traits\OfferingsEntityInterface;
use Ilios\CoreBundle\Traits\UsersEntityInterface;

/**
 * Interface InstructorGroupInterface
 */
interface InstructorGroupInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    OfferingsEntityInterface,
    SchoolEntityInterface,
    LoggableEntityInterface,
    LearnerGroupsEntityInterface,
    UsersEntityInterface,
    IlmSessionsEntityInterface
{
}
