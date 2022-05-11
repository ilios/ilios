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
    public function setNotes(?string $notes);
    public function getNotes(): ?string;

    public function setRequired(bool $required);
    public function isRequired(): bool;

    public function setPublicNotes(bool $publicNotes);
    public function hasPublicNotes(): bool;

    public function setLearningMaterial(LearningMaterialInterface $learningMaterial);
    public function getLearningMaterial(): LearningMaterialInterface;

    public function getStartDate(): ?DateTime;
    public function setStartDate(DateTime $startDate = null);

    public function getEndDate(): ?DateTime;
    public function setEndDate(DateTime $endDate = null);
}
