<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CourseObjectivesEntityInterface;
use App\Traits\IndexableCoursesEntityInterface;
use App\Traits\ProgramYearObjectivesEntityInterface;
use App\Traits\SessionObjectivesEntityInterface;
use Doctrine\Common\Collections\Collection;
use App\Traits\ActivatableEntityInterface;
use App\Traits\CoursesEntityInterface;
use App\Traits\DescribableNullableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\ProgramYearsEntityInterface;
use App\Traits\SessionsEntityInterface;
use App\Traits\TitledEntityInterface;

interface TermInterface extends
    DescribableNullableEntityInterface,
    IdentifiableEntityInterface,
    TitledEntityInterface,
    LoggableEntityInterface,
    ProgramYearsEntityInterface,
    SessionsEntityInterface,
    CoursesEntityInterface,
    ActivatableEntityInterface,
    IndexableCoursesEntityInterface,
    SessionObjectivesEntityInterface,
    CourseObjectivesEntityInterface,
    ProgramYearObjectivesEntityInterface
{
    public function setVocabulary(VocabularyInterface $vocabulary): void;
    public function getVocabulary(): VocabularyInterface;

    public function setParent(?TermInterface $parent = null): void;
    public function getParent(): ?TermInterface;

    public function setChildren(Collection $children): void;
    public function addChild(TermInterface $child): void;
    public function removeChild(TermInterface $child): void;
    public function getChildren(): Collection;
    public function hasChildren(): bool;

    public function setAamcResourceTypes(Collection $aamcResourceTypes): void;
    public function addAamcResourceType(AamcResourceTypeInterface $aamcResourceType): void;
    public function removeAamcResourceType(AamcResourceTypeInterface $aamcResourceType): void;
    public function getAamcResourceTypes(): Collection;
}
