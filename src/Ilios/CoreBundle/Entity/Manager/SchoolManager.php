<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\SchoolInterface;

/**
 * Class SchoolManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class SchoolManager extends BaseManager implements SchoolManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findSchoolBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findSchoolDTOBy(
        array $criteria,
        array $orderBy = null
    ) {
        $results = $this->getRepository()->findDTOsBy($criteria, $orderBy, 1);
        return empty($results)?false:$results[0];
    }

    /**
     * {@inheritdoc}
     */
    public function findSchoolsBy(
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
    public function findSchoolDTOsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findDTOsBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function updateSchool(
        SchoolInterface $school,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($school);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($school));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSchool(
        SchoolInterface $school
    ) {
        $this->em->remove($school);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createSchool()
    {
        $class = $this->getClass();
        return new $class();
    }

    /**
     * {@inheritdoc}
     */
    public function findEventsForSchool($schoolId, \DateTime $from, \DateTime $to)
    {
        return $this->getRepository()->findEventsForSchool($schoolId, $from, $to);
    }
}
