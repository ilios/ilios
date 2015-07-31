<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\DisciplineInterface;

/**
 * Class DisciplineManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class DisciplineManager extends AbstractManager implements DisciplineManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return DisciplineInterface
     */
    public function findDisciplineBy(
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
     * @return ArrayCollection|DisciplineInterface[]
     */
    public function findDisciplinesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param DisciplineInterface $discipline
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateDiscipline(
        DisciplineInterface $discipline,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($discipline);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($discipline));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param DisciplineInterface $discipline
     */
    public function deleteDiscipline(
        DisciplineInterface $discipline
    ) {
        $this->em->remove($discipline);
        $this->em->flush();
    }

    /**
     * @return DisciplineInterface
     */
    public function createDiscipline()
    {
        $class = $this->getClass();
        return new $class();
    }
}
