<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSessionInterface;

/**
 * Interface CurriculumInventorySequenceBlockSessionManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface CurriculumInventorySequenceBlockSessionManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CurriculumInventorySequenceBlockSessionInterface
     */
    public function findCurriculumInventorySequenceBlockSessionBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return CurriculumInventorySequenceBlockSessionInterface[]|Collection
     */
    public function findCurriculumInventorySequenceBlockSessionsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
     * @param bool $andFlush
     *
     * @return void
     */
     public function updateCurriculumInventorySequenceBlockSession(CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession, $andFlush = true);

    /**
     * @param CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
     *
     * @return void
     */
    public function deleteCurriculumInventorySequenceBlockSession(CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession);

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return CurriculumInventorySequenceBlockSessionInterface
     */
    public function createCurriculumInventorySequenceBlockSession();
}