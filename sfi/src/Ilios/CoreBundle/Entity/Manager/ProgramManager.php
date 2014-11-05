<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\ProgramManager as BaseProgramManager;
use Ilios\CoreBundle\Model\ProgramInterface;

class ProgramManager extends BaseProgramManager
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
     * @return ProgramInterface
     */
    public function findProgramBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * Previously known as findAllBy()
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return ProgramInterface[]|Collection
     */
    public function findProgramsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param ProgramInterface $program
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateProgram(ProgramInterface $program, $andFlush = true)
    {
        $this->em->persist($program);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param ProgramInterface $program
     *
     * @return void
     */
    public function deleteProgram(ProgramInterface $program)
    {
        $this->em->remove($program);
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
