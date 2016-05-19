<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\ProgramYearInterface;
use Ilios\CoreBundle\Entity\ProgramYearStewardInterface;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Ilios\CoreBundle\Traits\SchoolEntityInterface;

/**
 * Class ProgramYearStewardManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class ProgramYearStewardManager extends BaseManager implements ProgramYearStewardManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findProgramYearStewardBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findProgramYearStewardsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function updateProgramYearSteward(
        ProgramYearStewardInterface $programYearSteward,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($programYearSteward);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($programYearSteward));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteProgramYearSteward(
        ProgramYearStewardInterface $programYearSteward
    ) {
        $this->em->remove($programYearSteward);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createProgramYearSteward()
    {
        $class = $this->getClass();
        return new $class();
    }

    /**
     * {@inheritdoc}
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
