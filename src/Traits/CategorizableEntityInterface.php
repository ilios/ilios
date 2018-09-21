<?php

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\TermInterface;

/**
 * Interface CategorizableEntityInterface
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
     * @param TermInterface $term
     */
    public function removeTerm(TermInterface $term);

    /**
     * @return ArrayCollection|TermInterface[]
     */
    public function getTerms();
}
