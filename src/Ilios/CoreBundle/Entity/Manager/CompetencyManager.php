<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CompetencyInterface;

/**
 * Class CompetencyManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CompetencyManager extends AbstractManager implements CompetencyManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CompetencyInterface
     */
    public function findCompetencyBy(
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
     * @return ArrayCollection|CompetencyInterface[]
     */
    public function findCompetenciesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CompetencyInterface $competency
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateCompetency(
        CompetencyInterface $competency,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($competency);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($competency));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CompetencyInterface $competency
     */
    public function deleteCompetency(
        CompetencyInterface $competency
    ) {
        $this->em->remove($competency);
        $this->em->flush();
    }

    /**
     * @return CompetencyInterface
     */
    public function createCompetency()
    {
        $class = $this->getClass();
        return new $class();
    }
}
