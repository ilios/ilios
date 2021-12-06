<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\MeshDescriptorsEntityInterface;
use App\Traits\SortableEntityInterface;

/**
 * Interface LearningMaterialRelationshipInterface
 */
interface LearningMaterialRelationshipInterface extends
    IdentifiableEntityInterface,
    LoggableEntityInterface,
    MeshDescriptorsEntityInterface,
    SortableEntityInterface
{
    /**
     * @param string $notes
     */
    public function setNotes($notes);

    public function getNotes(): string;

    /**
     * @param bool $required
     */
    public function setRequired($required);

    public function isRequired(): bool;

    /**
     * @param bool $publicNotes
     */
    public function setPublicNotes($publicNotes);

    public function hasPublicNotes(): bool;

    public function setLearningMaterial(LearningMaterialInterface $learningMaterial);

    public function getLearningMaterial(): LearningMaterialInterface;

    public function getStartDate(): ?\DateTime;

    /**
     * @param \DateTime|null $startDate
     */
    public function setStartDate(\DateTime $startDate = null);

    public function getEndDate(): ?\DateTime;

    /**
     * @param \DateTime|null $endDate
     */
    public function setEndDate(\DateTime $endDate = null);
}
