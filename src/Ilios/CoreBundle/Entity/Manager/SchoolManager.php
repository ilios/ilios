<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\SchoolInterface;

/**
 * Class SchoolManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class SchoolManager implements SchoolManagerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param Registry $em
     * @param string $class
     */
    public function __construct(Registry $em, $class)
    {
        $this->em         = $em->getManagerForClass($class);
        $this->class      = $class;
        $this->repository = $em->getRepository($class);
    }

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
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return SchoolInterface
     */
    public function createSchool()
    {
        $class = $this->getClass();
        return new $class();
    }
}
