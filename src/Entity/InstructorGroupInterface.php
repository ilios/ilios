<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\IdentifiableEntityInterface;
use App\Entity\IlmSessionsEntityInterface;
use App\Entity\LearnerGroupsEntityInterface;
use App\Entity\SchoolEntityInterface;
use App\Entity\TitledEntityInterface;
use App\Entity\OfferingsEntityInterface;
use App\Entity\UsersEntityInterface;

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
