<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\SchoolManager as BaseSchoolManager;
use Ilios\CoreBundle\Model\SchoolInterface;

class SchoolManager extends BaseSchoolManager
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
     * @param EntityManager $em
     * @param string $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em         = $em;
        $this->class      = $class;
        $this->repository = $em->getRepository($class);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return SchoolInterface
     */
    public function findSchoolBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * Previously known as findAllBy()
     *
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return SchoolInterface[]|Collection
     */
    public function findSchoolsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param SchoolInterface $school
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateSchool(SchoolInterface $school, $andFlush = true)
    {
        $this->em->persist($school);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param SchoolInterface $school
     *
     * @return void
     */
    public function deleteSchool(SchoolInterface $school)
    {
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
}
