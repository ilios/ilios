<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\LearnerGroupsEntityInterface;
use Ilios\CoreBundle\Traits\SchoolEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;
use Ilios\CoreBundle\Traits\OfferingsEntityInterface;
use Ilios\CoreBundle\Traits\UsersEntityInterface;

/**
 * Interface InstructorGroupInterface
 * @package Ilios\CoreBundle\Entity
 */
interface InstructorGroupInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    OfferingsEntityInterface,
    SchoolEntityInterface,
    LoggableEntityInterface,
    LearnerGroupsEntityInterface,
    UsersEntityInterface
{
    /**
     * @param Collection $ilmSessions
     */
    public function setIlmSessions(Collection $ilmSessions);

    /**
     * @param IlmSessionInterface $ilmSession
     */
    public function addIlmSession(IlmSessionInterface $ilmSession);

    /**
     * @return ArrayCollection|IlmSessionInterface[]
     */
    public function getIlmSessions();
}
