<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\CurriculumInventorySequenceBlockManager as BaseCurriculumInventorySequenceBlockManager;
use Ilios\CoreBundle\Model\CurriculumInventorySequenceBlockInterface;

class CurriculumInventorySequenceBlockManager extends BaseCurriculumInventorySequenceBlockManager
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
     * @return CurriculumInventorySequenceBlockInterface
     */
    public function findCurriculumInventorySequenceBlockBy(array $criteria, array $orderBy = null)
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
     * @return CurriculumInventorySequenceBlockInterface[]|Collection
     */
    public function findCurriculumInventorySequenceBlocksBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCurriculumInventorySequenceBlock(CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock, $andFlush = true)
    {
        $this->em->persist($curriculumInventorySequenceBlock);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock
     *
     * @return void
     */
    public function deleteCurriculumInventorySequenceBlock(CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock)
    {
        $this->em->remove($curriculumInventorySequenceBlock);
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
