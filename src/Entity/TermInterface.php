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
use App\Traits\StringableEntityInterface;
use App\Traits\TitledEntityInterface;

interface TermInterface extends
    DescribableNullableEntityInterface,
    IdentifiableEntityInterface,
    StringableEntityInterface,
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
    public function setVocabulary(VocabularyInterface $vocabulary);
    public function getVocabulary(): VocabularyInterface;

    public function setParent(TermInterface $parent = null);
    public function getParent(): ?TermInterface;

    public function setChildren(Collection $children);
    public function addChild(TermInterface $child);
    public function removeChild(TermInterface $child);
    public function getChildren(): Collection;
    public function hasChildren(): bool;

    public function setAamcResourceTypes(Collection $aamcResourceTypes);
    public function addAamcResourceType(AamcResourceTypeInterface $aamcResourceType);
    public function removeAamcResourceType(AamcResourceTypeInterface $aamcResourceType);
    public function getAamcResourceTypes(): Collection;
}
