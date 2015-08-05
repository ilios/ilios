<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\SchoolInterface;

/**
 * Class SchoolManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class SchoolManager extends AbstractManager implements SchoolManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return SchoolInterface
     */
    public function findSchoolBy(
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
     * @return ArrayCollection|SchoolInterface[]
     */
    public function findSchoolsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param SchoolInterface $school
     * @param bool $andFlush
     * @param bool $forceId
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
     * @param SchoolInterface $school
     */
    public function deleteSchool(
        SchoolInterface $school
    ) {
        $this->em->remove($school);
        $this->em->flush();
    }

    /**
     * @return SchoolInterface
     */
    public function createSchool()
    {
        $class = $this->getClass();
        return new $class();
    }

    /**
     * @param integer $schoolId
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return UserEvent[]|Collection
     */
    public function findEventsForSchool(
        $schoolId,
        \DateTime $from,
        \DateTime $to
    ) {
        return $this->repository->findEventsForSchool($schoolId, $from, $to);
    }
}
