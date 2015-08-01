<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\ProgramYearInterface;

/**
 * Class ProgramYearManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class ProgramYearManager extends AbstractManager implements ProgramYearManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return ProgramYearInterface
     */
    public function findProgramYearBy(
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
     * @return ArrayCollection|ProgramYearInterface[]
     */
    public function findProgramYearsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param ProgramYearInterface $programYear
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateProgramYear(
        ProgramYearInterface $programYear,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($programYear);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($programYear));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param ProgramYearInterface $programYear
     */
    public function deleteProgramYear(
        ProgramYearInterface $programYear
    ) {
        $this->em->remove($programYear);
        $this->em->flush();
    }

    /**
     * @return ProgramYearInterface
     */
    public function createProgramYear()
    {
        $class = $this->getClass();
        return new $class();
    }
}
