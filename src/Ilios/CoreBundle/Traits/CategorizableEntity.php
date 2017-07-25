<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Entity\TermInterface;

/**
 * Class CategorizableEntity
 */
trait CategorizableEntity
{
    /**
     * @param Collection|TermInterface[] $terms
     */
    public function setTerms(Collection $terms = null)
    {
        $this->terms = new ArrayCollection();
        if (is_null($terms)) {
            return;
        }

        foreach ($terms as $term) {
            $this->addTerm($term);
        }
    }

    /**
     * @param TermInterface $term
     */
    public function addTerm(TermInterface $term)
    {
        if (!$this->terms->contains($term)) {
            $this->terms->add($term);
        }
    }

    /**
     * @param TermInterface $term
     */
    public function removeTerm(TermInterface $term)
    {
        $this->terms->removeElement($term);
    }

    /**
     * @return ArrayCollection|TermInterface[]
     */
    public function getTerms()
    {
        return $this->terms;
    }
}
