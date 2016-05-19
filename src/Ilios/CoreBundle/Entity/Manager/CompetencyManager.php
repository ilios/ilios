<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CompetencyInterface;

/**
 * Class CompetencyManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CompetencyManager extends BaseManager implements CompetencyManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findCompetencyBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findCompetenciesBy(
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
     * {@inheritdoc}
     */
    public function deleteCompetency(
        CompetencyInterface $competency
    ) {
        $this->em->remove($competency);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createCompetency()
    {
        $class = $this->getClass();
        return new $class();
    }
}
