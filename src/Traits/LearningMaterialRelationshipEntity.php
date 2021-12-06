<?php

declare(strict_types=1);

namespace App\Traits;

use App\Entity\LearningMaterialInterface;
use App\Entity\LearningMaterialRelationshipInterface;

/**
 * Trait LearningMaterialRelationshipEntity
 * @package App\Traits
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
    public function getNotes(): string
    {
        return $this->notes;
    }

    /**
     * @param bool $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param bool $publicNotes
     */
    public function setPublicNotes($publicNotes)
    {
        $this->publicNotes = $publicNotes;
    }

    /**
     * @return bool
     */
    public function hasPublicNotes(): bool
    {
        return $this->publicNotes;
    }

    public function setLearningMaterial(LearningMaterialInterface $learningMaterial)
    {
        $this->learningMaterial = $learningMaterial;
    }

    /**
     * @return LearningMaterialInterface
     */
    public function getLearningMaterial(): LearningMaterialInterface
    {
        return $this->learningMaterial;
    }

    public function setStartDate(\DateTime $startDate = null)
    {
        $this->startDate = $startDate;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setEndDate(\DateTime $endDate = null)
    {
        $this->endDate = $endDate;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }
}
