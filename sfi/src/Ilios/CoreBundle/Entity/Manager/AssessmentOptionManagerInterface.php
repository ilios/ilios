<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\AssessmentOptionInterface;

/**
 * Interface AssessmentOptionManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface AssessmentOptionManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return AssessmentOptionInterface
     */
    public function findAssessmentOptionBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return AssessmentOptionInterface[]|Collection
     */
    public function findAssessmentOptionsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param AssessmentOptionInterface $assessmentOption
     * @param bool $andFlush
     *
     * @return void
     */
     public function updateAssessmentOption(AssessmentOptionInterface $assessmentOption, $andFlush = true);

    /**
     * @param AssessmentOptionInterface $assessmentOption
     *
     * @return void
     */
    public function deleteAssessmentOption(AssessmentOptionInterface $assessmentOption);

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return AssessmentOptionInterface
     */
    public function createAssessmentOption();
}