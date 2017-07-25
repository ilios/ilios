<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\ActivatableEntityInterface;
use Ilios\CoreBundle\Traits\CoursesEntityInterface;
use Ilios\CoreBundle\Traits\DescribableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\ProgramYearsEntityInterface;
use Ilios\CoreBundle\Traits\SessionsEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;

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
    ActivatableEntityInterface
{
    /**
     * @param VocabularyInterface $vocabulary
     */
    public function setVocabulary(VocabularyInterface $vocabulary);

    /**
     * @return VocabularyInterface
     */
    public function getVocabulary();

    /**
     * @param TermInterface $parent
     */
    public function setParent(TermInterface $parent);

    /**
     * @return TermInterface
     */
    public function getParent();

    /**
     * @param Collection $children
     */
    public function setChildren(Collection $children);

    /**
     * @param TermInterface $child
     */
    public function addChild(TermInterface $child);

    /**
     * @param TermInterface $child
     */
    public function removeChild(TermInterface $child);

    /**
     * @return ArrayCollection|TermInterface[]
     */
    public function getChildren();

    /**
     * @return boolean
     */
    public function hasChildren();

    /**
     * @param Collection $aamcResourceTypes
     */
    public function setAamcResourceTypes(Collection $aamcResourceTypes);

    /**
     * @param AamcResourceTypeInterface $aamcResourceType
     */
    public function addAamcResourceType(AamcResourceTypeInterface $aamcResourceType);

    /**
     * @param AamcResourceTypeInterface $aamcResourceType
     */
    public function removeAamcResourceType(AamcResourceTypeInterface $aamcResourceType);

    /**
     * @return ArrayCollection|AamcResourceTypeInterface[]
     */
    public function getAamcResourceTypes();
}
