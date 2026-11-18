<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\ActivatableEntityInterface;
use App\Entity\CourseObjectivesEntityInterface;
use App\Entity\CoursesEntityInterface;
use App\Entity\DescribableNullableEntityInterface;
use App\Entity\IdentifiableEntityInterface;
use App\Entity\IndexableCoursesEntityInterface;
use App\Entity\ProgramYearObjectivesEntityInterface;
use App\Entity\ProgramYearsEntityInterface;
use App\Entity\SessionObjectivesEntityInterface;
use App\Entity\SessionsEntityInterface;
use App\Entity\TitledEntityInterface;
use Doctrine\Common\Collections\Collection;

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
