<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CohortInterface;

/**
 * Class CohortManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CohortManager extends AbstractManager implements CohortManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findCohortBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findCohortDTOBy(
        array $criteria,
        array $orderBy = null
    ) {
        $results = $this->getRepository()->findDTOsBy($criteria, $orderBy, 1);
        return empty($results)?false:$results[0];
    }

    /**
     * {@inheritdoc}
     */
    public function findCohortsBy(
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
    public function findCohortDTOsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findDTOsBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CohortInterface $cohort
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateCohort(
        CohortInterface $cohort,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($cohort);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($cohort));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CohortInterface $cohort
     */
    public function deleteCohort(
        CohortInterface $cohort
    ) {
        $this->em->remove($cohort);
        $this->em->flush();
    }

    /**
     * @return CohortInterface
     */
    public function createCohort()
    {
        $class = $this->getClass();
        return new $class();
    }
}
