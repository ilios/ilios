<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\InstructionHoursManager as BaseInstructionHoursManager;
use Ilios\CoreBundle\Model\InstructionHoursInterface;

class InstructionHoursManager extends BaseInstructionHoursManager
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
     * Previously known as findAllBy()
     *
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return InstructionHoursInterface[]|Collection
     */
    public function findInstructionHoursBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param InstructionHoursInterface $instructionHours
     * @param bool $andFlush
     *
     * @return void
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
     *
     * @return void
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
}
