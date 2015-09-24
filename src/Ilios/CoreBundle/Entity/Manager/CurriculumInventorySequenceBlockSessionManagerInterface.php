<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSessionInterface;

/**
 * Interface CurriculumInventorySequenceBlockSessionManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface CurriculumInventorySequenceBlockSessionManagerInterface extends ManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findCurriculumInventorySequenceBlockSessionBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * {@inheritdoc}
     */
    public function findCurriculumInventorySequenceBlockSessionsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * {@inheritdoc}
     */
    public function updateCurriculumInventorySequenceBlockSession(
        CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession,
        $andFlush = true,
        $forceId = false
    );

    /**
     * {@inheritdoc}
     */
    public function deleteCurriculumInventorySequenceBlockSession(
        CurriculumInventorySequenceBlockSessionInterface $curriculumInventorySequenceBlockSession
    );

    /**
     * {@inheritdoc}
     */
    public function createCurriculumInventorySequenceBlockSession();
}
