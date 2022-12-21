<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\MeshConceptInterface;

/**
 * Class ConceptsEntity
 */
trait ConceptsEntity
{
    protected Collection $concepts;

    public function setConcepts(Collection $concepts)
    {
        $this->concepts = new ArrayCollection();

        foreach ($concepts as $concept) {
            $this->addConcept($concept);
        }
    }

    public function addConcept(MeshConceptInterface $concept)
    {
        if (!$this->concepts->contains($concept)) {
            $this->concepts->add($concept);
        }
    }

    public function removeConcept(MeshConceptInterface $concept)
    {
        $this->concepts->removeElement($concept);
    }

    public function getConcepts(): Collection
    {
        return $this->concepts;
    }
}
