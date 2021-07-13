<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\CourseObjectivesEntityInterface;
use App\Traits\IndexableCoursesEntityInterface;
use App\Traits\ProgramYearObjectivesEntityInterface;
use App\Traits\SessionObjectivesEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\ActivatableEntityInterface;
use App\Traits\CoursesEntityInterface;
use App\Traits\DescribableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\ProgramYearsEntityInterface;
use App\Traits\SessionsEntityInterface;
use App\Traits\StringableEntityInterface;
use App\Traits\TitledEntityInterface;

/**
 * Interface TermInterface
 */
interface TermInterface extends
    DescribableEntityInterface,
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

    /**
     * @return VocabularyInterface
     */
    public function getVocabulary();

    public function setParent(TermInterface $parent = null);

    /**
     * @return TermInterface
     */
    public function getParent();

    public function setChildren(Collection $children);

    public function addChild(TermInterface $child);

    public function removeChild(TermInterface $child);

    /**
     * @return ArrayCollection|TermInterface[]
     */
    public function getChildren();

    /**
     * @return bool
     */
    public function hasChildren();

    public function setAamcResourceTypes(Collection $aamcResourceTypes);

    public function addAamcResourceType(AamcResourceTypeInterface $aamcResourceType);

    public function removeAamcResourceType(AamcResourceTypeInterface $aamcResourceType);

    /**
     * @return ArrayCollection|AamcResourceTypeInterface[]
     */
    public function getAamcResourceTypes();
}
