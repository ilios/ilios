<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\SchoolEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;

/**
 * Interface VocabularyInterface
 * @package Ilios\CoreBundle\Entity
 */
interface VocabularyInterface extends
    IdentifiableEntityInterface,
    SchoolEntityInterface,
    StringableEntityInterface,
    TitledEntityInterface
{
    /**
     * @param Collection $terms
     */
    public function setTerms(Collection $terms);

    /**
     * @param TermInterface $term
     */
    public function addTerm(TermInterface $term);

    /**
     * @return ArrayCollection|TermInterface[]
     */
    public function getTerms();
}
