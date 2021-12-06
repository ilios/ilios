<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CreatedAtEntityInterface;
use App\Traits\CourseObjectivesEntityInterface;
use App\Traits\IndexableCoursesEntityInterface;
use App\Traits\ProgramYearObjectivesEntityInterface;
use App\Traits\SessionObjectivesEntityInterface;
use Doctrine\Common\Collections\Collection;
use App\Traits\ConceptsEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\NameableEntityInterface;
use App\Traits\TimestampableEntityInterface;
use App\Traits\CoursesEntityInterface;
use App\Traits\SessionsEntityInterface;

/**
 * Interface MeshDescriptorInterface
 */
interface MeshDescriptorInterface extends
    IdentifiableEntityInterface,
    NameableEntityInterface,
    TimestampableEntityInterface,
    CoursesEntityInterface,
    SessionsEntityInterface,
    ConceptsEntityInterface,
    IndexableCoursesEntityInterface,
    CreatedAtEntityInterface,
    SessionObjectivesEntityInterface,
    CourseObjectivesEntityInterface,
    ProgramYearObjectivesEntityInterface
{
    /**
     * @param string $annotation
     */
    public function setAnnotation($annotation);

    /**
     * @return string
     */
    public function getAnnotation(): string;

    public function setSessionLearningMaterials(Collection $sessionLearningMaterials);

    public function addSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial);

    public function removeSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial);

    /**
     * @return Collection
     */
    public function getSessionLearningMaterials(): Collection;

    public function setCourseLearningMaterials(Collection $courseLearningMaterials);

    public function addCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial);

    public function removeCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial);

    /**
     * @return Collection
     */
    public function getCourseLearningMaterials(): Collection;

    public function setQualifiers(Collection $qualifiers);

    public function addQualifier(MeshQualifierInterface $qualifier);

    public function removeQualifier(MeshQualifierInterface $qualifier);

    /**
     * @return Collection
     */
    public function getQualifiers(): Collection;

    public function setTrees(Collection $trees);

    public function addTree(MeshTreeInterface $tree);

    public function removeTree(MeshTreeInterface $tree);

    /**
     * @return Collection
     */
    public function getTrees(): Collection;

    public function setPreviousIndexing(MeshPreviousIndexingInterface $previousIndexing);

    /**
     * @return MeshPreviousIndexingInterface
     */
    public function getPreviousIndexing(): MeshPreviousIndexingInterface;

    /**
     * @return bool
     */
    public function isDeleted(): bool;

    /**
     * @param bool $deleted
     */
    public function setDeleted($deleted);
}
