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

    /**
     * @return string
     */
    public function getNotes();

    /**
     * @param bool $required
     */
    public function setRequired($required);

    /**
     * @return bool
     */
    public function isRequired();

    /**
     * @param bool $publicNotes
     */
    public function setPublicNotes($publicNotes);

    /**
     * @return bool
     */
    public function hasPublicNotes();

    public function setLearningMaterial(LearningMaterialInterface $learningMaterial);

    /**
     * @return LearningMaterialInterface
     */
    public function getLearningMaterial();

    /**
     * @return \DateTime|null
     */
    public function getStartDate();

    /**
     * @param \DateTime|null $startDate
     */
    public function setStartDate(\DateTime $startDate = null);

    /**
     * @return \DateTime|null
     */
    public function getEndDate();

    /**
     * @param \DateTime|null $endDate
     */
    public function setEndDate(\DateTime $endDate = null);
}
