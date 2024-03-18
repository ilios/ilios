<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CreatedAtEntityInterface;
use App\Traits\CourseObjectivesEntityInterface;
use App\Traits\IdentifiableStringEntityInterface;
use App\Traits\IndexableCoursesEntityInterface;
use App\Traits\ProgramYearObjectivesEntityInterface;
use App\Traits\SessionObjectivesEntityInterface;
use Doctrine\Common\Collections\Collection;
use App\Traits\ConceptsEntityInterface;
use App\Traits\NameableEntityInterface;
use App\Traits\TimestampableEntityInterface;
use App\Traits\CoursesEntityInterface;
use App\Traits\SessionsEntityInterface;

interface MeshDescriptorInterface extends
    IdentifiableStringEntityInterface,
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
    public function setAnnotation(?string $annotation): void;
    public function getAnnotation(): ?string;

    public function setSessionLearningMaterials(Collection $sessionLearningMaterials): void;
    public function addSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial): void;
    public function removeSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial): void;
    public function getSessionLearningMaterials(): Collection;

    public function setCourseLearningMaterials(Collection $courseLearningMaterials): void;
    public function addCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial): void;
    public function removeCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial): void;
    public function getCourseLearningMaterials(): Collection;

    public function setQualifiers(Collection $qualifiers): void;
    public function addQualifier(MeshQualifierInterface $qualifier): void;
    public function removeQualifier(MeshQualifierInterface $qualifier): void;
    public function getQualifiers(): Collection;

    public function setTrees(Collection $trees): void;
    public function addTree(MeshTreeInterface $tree): void;
    public function removeTree(MeshTreeInterface $tree): void;
    public function getTrees(): Collection;

    public function setPreviousIndexing(MeshPreviousIndexingInterface $previousIndexing): void;
    public function getPreviousIndexing(): MeshPreviousIndexingInterface;

    public function isDeleted(): bool;
    public function setDeleted(bool $deleted): void;
}
