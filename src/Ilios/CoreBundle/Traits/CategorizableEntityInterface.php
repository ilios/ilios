<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\TermInterface;

/**
 * Interface CategorizableEntityInterface
 * @package Ilios\CoreBundle\Traits
 */
interface CategorizableEntityInterface
{
    /**
     * @param Collection|TermInterface[] $terms
     */
    public function setTerms(Collection $terms = null);

    /**
     * @param TermInterface $term
     */
    public function addTerm(TermInterface $term);

    /**
     * @return ArrayCollection|TermInterface[]
     */
    public function getTerms();
}
