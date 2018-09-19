<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Traits\IdentifiableEntityInterface;
use App\Traits\IlmSessionsEntityInterface;
use App\Traits\LearnerGroupsEntityInterface;
use App\Traits\SchoolEntityInterface;
use App\Traits\TitledEntityInterface;
use App\Traits\OfferingsEntityInterface;
use App\Traits\UsersEntityInterface;

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
