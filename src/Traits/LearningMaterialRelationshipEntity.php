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
    protected ?string $notes = null;

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setPublicNotes(bool $publicNotes): void
    {
        $this->publicNotes = $publicNotes;
    }

    public function hasPublicNotes(): bool
    {
        return $this->publicNotes;
    }

    public function setLearningMaterial(LearningMaterialInterface $learningMaterial): void
    {
        $this->learningMaterial = $learningMaterial;
    }

    public function getLearningMaterial(): LearningMaterialInterface
    {
        return $this->learningMaterial;
    }

    public function setStartDate(?DateTime $startDate = null): void
    {
        $this->startDate = $startDate;
    }

    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    public function setEndDate(?DateTime $endDate = null): void
    {
        $this->endDate = $endDate;
    }

    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }
}
