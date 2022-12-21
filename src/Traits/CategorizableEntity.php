<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\TermInterface;

trait CategorizableEntity
{
    protected Collection $terms;

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

    public function addTerm(TermInterface $term)
    {
        if (!$this->terms->contains($term)) {
            $this->terms->add($term);
        }
    }

    public function removeTerm(TermInterface $term)
    {
        $this->terms->removeElement($term);
    }

    public function getTerms(): Collection
    {
        return $this->terms;
    }
}
