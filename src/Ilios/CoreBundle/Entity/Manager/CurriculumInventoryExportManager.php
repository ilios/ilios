<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CurriculumInventoryExportInterface;

/**
 * Class CurriculumInventoryExportManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CurriculumInventoryExportManager implements CurriculumInventoryExportManagerInterface
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
     * @return CurriculumInventoryExportInterface
     */
    public function findCurriculumInventoryExportBy(
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
     * @return ArrayCollection|CurriculumInventoryExportInterface[]
     */
    public function findCurriculumInventoryExportsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CurriculumInventoryExportInterface $curriculumInventoryExport
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateCurriculumInventoryExport(
        CurriculumInventoryExportInterface $curriculumInventoryExport,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($curriculumInventoryExport);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($curriculumInventoryExport));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CurriculumInventoryExportInterface $curriculumInventoryExport
     */
    public function deleteCurriculumInventoryExport(
        CurriculumInventoryExportInterface $curriculumInventoryExport
    ) {
        $this->em->remove($curriculumInventoryExport);
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
     * @return CurriculumInventoryExportInterface
     */
    public function createCurriculumInventoryExport()
    {
        $class = $this->getClass();
        return new $class();
    }
}
