<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use AppBundle\Traits\IdentifiableEntityInterface;
use AppBundle\Traits\IlmSessionsEntityInterface;
use AppBundle\Traits\LearnerGroupsEntityInterface;
use AppBundle\Traits\SchoolEntityInterface;
use AppBundle\Traits\TitledEntityInterface;
use AppBundle\Traits\OfferingsEntityInterface;
use AppBundle\Traits\UsersEntityInterface;

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
