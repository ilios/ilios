<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;

/**
 * Class CurriculumInventorySequenceBlockManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CurriculumInventorySequenceBlockManager implements CurriculumInventorySequenceBlockManagerInterface
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
     * @return CurriculumInventorySequenceBlockInterface
     */
    public function findCurriculumInventorySequenceBlockBy(
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
     * @return ArrayCollection|CurriculumInventorySequenceBlockInterface[]
     */
    public function findCurriculumInventorySequenceBlocksBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateCurriculumInventorySequenceBlock(
        CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock,
        $andFlush = true,
        $forceId  = false
    ) {
        $this->em->persist($curriculumInventorySequenceBlock);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($curriculumInventorySequenceBlock));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock
     */
    public function deleteCurriculumInventorySequenceBlock(
        CurriculumInventorySequenceBlockInterface $curriculumInventorySequenceBlock
    ) {
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

    /**
     * @return CurriculumInventorySequenceBlockInterface
     */
    public function createCurriculumInventorySequenceBlock()
    {
        $class = $this->getClass();
        return new $class();
    }
}
