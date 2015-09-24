<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\ProgramYearInterface;

/**
 * Class ProgramYearManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class ProgramYearManager extends AbstractManager implements ProgramYearManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findProgramYearBy(
        array $criteria,
        array $orderBy = null
    ) {
        $criteria['deleted'] = false;
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findProgramYearsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        $criteria['deleted'] = false;
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function deleteProgramYear(
        ProgramYearInterface $programYear
    ) {
        $programYear->setDeleted(true);
        $this->updateProgramYear($programYear);
    }

    /**
     * {@inheritdoc}
     */
    public function createProgramYear()
    {
        $class = $this->getClass();
        return new $class();
    }
}
