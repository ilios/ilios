<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\MeshDescriptorsEntityInterface;
use App\Traits\SortableEntityInterface;

interface LearningMaterialRelationshipInterface extends
    IdentifiableEntityInterface,
    LoggableEntityInterface,
    MeshDescriptorsEntityInterface,
    SortableEntityInterface
{
    public function setNotes(?string $notes): void;
    public function getNotes(): ?string;

    public function setRequired(bool $required): void;
    public function isRequired(): bool;

    public function setPublicNotes(bool $publicNotes): void;
    public function hasPublicNotes(): bool;

    public function setLearningMaterial(LearningMaterialInterface $learningMaterial): void;
    public function getLearningMaterial(): LearningMaterialInterface;

    public function getStartDate(): ?DateTime;
    public function setStartDate(?DateTime $startDate = null): void;

    public function getEndDate(): ?DateTime;
    public function setEndDate(?DateTime $endDate = null): void;
}
