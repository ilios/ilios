<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\CurriculumInventorySequenceManager as BaseCurriculumInventorySequenceManager;
use Ilios\CoreBundle\Model\CurriculumInventorySequenceInterface;

class CurriculumInventorySequenceManager extends BaseCurriculumInventorySequenceManager
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
     * @return CurriculumInventorySequenceInterface
     */
    public function findCurriculumInventorySequenceBy(array $criteria, array $orderBy = null)
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
     * @return CurriculumInventorySequenceInterface[]|Collection
     */
    public function findCurriculumInventorySequencesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CurriculumInventorySequenceInterface $curriculumInventorySequence
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCurriculumInventorySequence(CurriculumInventorySequenceInterface $curriculumInventorySequence, $andFlush = true)
    {
        $this->em->persist($curriculumInventorySequence);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CurriculumInventorySequenceInterface $curriculumInventorySequence
     *
     * @return void
     */
    public function deleteCurriculumInventorySequence(CurriculumInventorySequenceInterface $curriculumInventorySequence)
    {
        $this->em->remove($curriculumInventorySequence);
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
