<?php

namespace Ilios\CoreBundle\Traits;

use Ilios\CoreBundle\Entity\LearningMaterialInterface;
use Ilios\CoreBundle\Entity\LearningMaterialRelationshipInterface;

/**
 * Trait LearningMaterialRelationshipEntity
 * @package Ilios\CoreBundle\Traits
 * @see LearningMaterialRelationshipInterface
 */
trait LearningMaterialRelationshipEntity
{
    /**
     * @param string $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param boolean $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param boolean $publicNotes
     */
    public function setPublicNotes($publicNotes)
    {
        $this->publicNotes = $publicNotes;
    }

    /**
     * @return boolean
     */
    public function hasPublicNotes()
    {
        return $this->publicNotes;
    }

    /**
     * @param LearningMaterialInterface $learningMaterial
     */
    public function setLearningMaterial(LearningMaterialInterface $learningMaterial)
    {
        $this->learningMaterial = $learningMaterial;
    }

    /**
     * @return LearningMaterialInterface
     */
    public function getLearningMaterial()
    {
        return $this->learningMaterial;
    }

    /**
     * @inheritdoc
     */
    public function setStartDate(\DateTime $startDate = null)
    {
        $this->startDate = $startDate;
    }

    /**
     * @inheritdoc
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @inheritdoc
     */
    public function setEndDate(\DateTime $endDate = null)
    {
        $this->endDate = $endDate;
    }

    /**
     * @inheritdoc
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
}
