<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\CurriculumInventoryExportManager as BaseCurriculumInventoryExportManager;
use Ilios\CoreBundle\Model\CurriculumInventoryExportInterface;

class CurriculumInventoryExportManager extends BaseCurriculumInventoryExportManager
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
     * @return CurriculumInventoryExportInterface
     */
    public function findCurriculumInventoryExportBy(array $criteria, array $orderBy = null)
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
     * @return CurriculumInventoryExportInterface[]|Collection
     */
    public function findCurriculumInventoryExportsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CurriculumInventoryExportInterface $curriculumInventoryExport
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCurriculumInventoryExport(CurriculumInventoryExportInterface $curriculumInventoryExport, $andFlush = true)
    {
        $this->em->persist($curriculumInventoryExport);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CurriculumInventoryExportInterface $curriculumInventoryExport
     *
     * @return void
     */
    public function deleteCurriculumInventoryExport(CurriculumInventoryExportInterface $curriculumInventoryExport)
    {
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
}
