<?php

declare(strict_types=1);

namespace App\Traits;

use App\Entity\LearningMaterialInterface;
use App\Entity\LearningMaterialRelationshipInterface;
use DateTime;

/**
 * Trait LearningMaterialRelationshipEntity
 * @package App\Traits
 * @see LearningMaterialRelationshipInterface
 */
trait LearningMaterialRelationshipEntity
{
    protected ?string $notes;

    /**
     * @param string|null $notes
     */
    public function setNotes(?string $notes)
    {
        $this->notes = $notes;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * @param bool $required
     */
    public function setRequired(bool $required)
    {
        $this->required = $required;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @param bool $publicNotes
     */
    public function setPublicNotes(bool $publicNotes)
    {
        $this->publicNotes = $publicNotes;
    }

    public function hasPublicNotes(): bool
    {
        return $this->publicNotes;
    }

    public function setLearningMaterial(LearningMaterialInterface $learningMaterial)
    {
        $this->learningMaterial = $learningMaterial;
    }

    public function getLearningMaterial(): LearningMaterialInterface
    {
        return $this->learningMaterial;
    }

    public function setStartDate(DateTime $startDate = null)
    {
        $this->startDate = $startDate;
    }

    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    public function setEndDate(DateTime $endDate = null)
    {
        $this->endDate = $endDate;
    }

    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }
}
