<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\ProgramInterface;

/**
 * Class ProgramManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class ProgramManager extends AbstractManager implements ProgramManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return ProgramInterface
     */
    public function findProgramBy(
        array $criteria,
        array $orderBy = null
    ) {
        $criteria['deleted'] = false;
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|ProgramInterface[]
     */
    public function findProgramsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        $criteria['deleted'] = false;
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param ProgramInterface $program
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateProgram(
        ProgramInterface $program,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($program);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($program));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param ProgramInterface $program
     */
    public function deleteProgram(
        ProgramInterface $program
    ) {
        $program->setDeleted(true);
        $this->updateProgram($program);
    }

    /**
     * @return ProgramInterface
     */
    public function createProgram()
    {
        $class = $this->getClass();
        return new $class();
    }
}
