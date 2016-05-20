<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\ProgramYearInterface;
use Ilios\CoreBundle\Entity\ProgramYearStewardInterface;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Ilios\CoreBundle\Traits\SchoolEntityInterface;

/**
 * Class ProgramYearStewardManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class ProgramYearStewardManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findProgramYearStewardBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findProgramYearStewardsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateProgramYearSteward(
        ProgramYearStewardInterface $programYearSteward,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($programYearSteward, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteProgramYearSteward(
        ProgramYearStewardInterface $programYearSteward
    ) {
        $this->delete($programYearSteward);
    }

    /**
     * @deprecated
     */
    public function createProgramYearSteward()
    {
        return $this->create();
    }

    /**
     * Checks if a given entity's school (co-)stewards a given program year.
     *
     * @param SchoolEntityInterface $schoolEntity the entity with a school
     * @param ProgramYearInterface $programYear
     * @return bool
     */
    public function schoolIsStewardingProgramYear(
        SchoolEntityInterface $schoolEntity,
        ProgramYearInterface $programYear
    ) {
        $school = $schoolEntity->getSchool();
        if (! $school instanceof SchoolInterface) {
            return false;
        }
        $criteria = ['programYear' => $programYear->getId()];
        $stewards = $this->findProgramYearStewardsBy($criteria);
        foreach ($stewards as $steward) {
            $stewardingSchool = $steward->getSchool();
            if ($stewardingSchool instanceof SchoolInterface
                && $school->getId() === $stewardingSchool->getId()) {
                return true;
            }
        }
        return false;
    }
}
