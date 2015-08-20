<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\SchoolEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;
use Ilios\CoreBundle\Traits\OfferingsEntityInterface;

/**
 * Interface InstructorGroupInterface
 * @package Ilios\CoreBundle\Entity
 */
interface InstructorGroupInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    OfferingsEntityInterface,
    SchoolEntityInterface
{
    /**
     * @param Collection $learnerGroups
     */
    public function setLearnerGroups(Collection $learnerGroups);

    /**
     * @param LearnerGroupInterface $learnerGroup
     */
    public function addLearnerGroup(LearnerGroupInterface $learnerGroup);

    /**
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function getLearnerGroups();

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

    /**
     * @param Collection $users
     */
    public function setUsers(Collection $users);

    /**
     * @param UserInterface $user
     */
    public function addUser(UserInterface $user);

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getUsers();
}
