<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\ProgramYearStewardInterface;

/**
 * Class ProgramYearStewardManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class ProgramYearStewardManager extends AbstractManager implements ProgramYearStewardManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return ProgramYearStewardInterface
     */
    public function findProgramYearStewardBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|ProgramYearStewardInterface[]
     */
    public function findProgramYearStewardsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param ProgramYearStewardInterface $programYearSteward
     * @param bool $andFlush
     * @param bool $forceId
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
     * @param ProgramYearStewardInterface $programYearSteward
     */
    public function deleteProgramYearSteward(
        ProgramYearStewardInterface $programYearSteward
    ) {
        $this->em->remove($programYearSteward);
        $this->em->flush();
    }

    /**
     * @return ProgramYearStewardInterface
     */
    public function createProgramYearSteward()
    {
        $class = $this->getClass();
        return new $class();
    }
}
