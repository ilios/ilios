<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Ilios\CoreBundle\Model\Manager\CurriculumInventorySequenceBlockSessionManager as BaseCurriculumInventorySequenceBlockSessionManager;
use Ilios\CoreBundle\Model\CurriculumInventorySequenceBlockSessionInterface;

class CurriculumInventorySequenceBlockSessionManager extends BaseCurriculumInventorySequenceBlockSessionManager
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
     * @return CurriculumInventorySequenceBlockSessionInterface
     */
    public function findCurriculumInventorySequenceBlockSessionBy(array $criteria, array $orderBy = null)
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
     * @return CurriculumInventorySequenceBlockSessionInterface[]|Collection
     */
    public function findCurriculumInventorySequenceBlockSessionsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCurriculumInventorySequenceBlockSession(CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession, $andFlush = true)
    {
        $this->em->persist($curriculumInventorySequenceBlockSession);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
     *
     * @return void
     */
    public function deleteCurriculumInventorySequenceBlockSession(CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession)
    {
        $this->em->remove($curriculumInventorySequenceBlockSession);
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
