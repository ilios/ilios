<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;

/**
 * Interface CurriculumInventoryReportEventInterface
 * @package Ilios\CoreBundle\Entity
 */
interface CurriculumInventoryReportEventInterface
{

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     */
    public function setDescription($description);

    /**
     * @return int
     */
    public function getMethodId();

    /**
     * @param int $methodId
     */
    public function setMethodId($methodId);

    /**
     * @return boolean
     */
    public function isAssessmentMethod();

    /**
     * @param int|boolean $assessmentMethod
     */
    public function setAssessmentMethod($assessmentMethod);

    /**
     * @return string
     */
    public function getAssessmentOption();

    /**
     * @param string $assessmentOption
     */
    public function setAssessmentOption($assessmentOption);

    /**
     * @return int
     */
    public function getDuration();


    /**
     * @param int $duration
     */
    public function setDuration($duration);
}
