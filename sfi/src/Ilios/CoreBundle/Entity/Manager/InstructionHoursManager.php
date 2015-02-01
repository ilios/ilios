<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\InstructionHoursInterface;

/**
 * InstructionHours manager service.
 * Class InstructionHoursManager
 * @package Ilios\CoreBundle\Manager
 */
class InstructionHoursManager implements InstructionHoursManagerInterface
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
     * @return InstructionHoursInterface
     */
    public function findInstructionHoursBy(array $criteria, array $orderBy = null)
    {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return InstructionHoursInterface[]|Collection
     */
    public function findAllInstructionHoursBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param InstructionHoursInterface $instructionHours
     * @param bool $andFlush
     */
    public function updateInstructionHours(InstructionHoursInterface $instructionHours, $andFlush = true)
    {
        $this->em->persist($instructionHours);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param InstructionHoursInterface $instructionHours
     */
    public function deleteInstructionHours(InstructionHoursInterface $instructionHours)
    {
        $this->em->remove($instructionHours);
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
     * @return InstructionHoursInterface
     */
    public function createInstructionHours()
    {
        $class = $this->getClass();
        return new $class();
    }
}
