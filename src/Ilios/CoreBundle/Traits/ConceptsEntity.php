<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\MeshConceptInterface;

/**
 * Class ConceptsEntity
 */
trait ConceptsEntity
{
    /**
     * @param Collection $concepts
     */
    public function setConcepts(Collection $concepts)
    {
        $this->concepts = new ArrayCollection();

        foreach ($concepts as $concept) {
            $this->addConcept($concept);
        }
    }

    /**
     * @param MeshConceptInterface $concept
     */
    public function addConcept(MeshConceptInterface $concept)
    {
        if (!$this->concepts->contains($concept)) {
            $this->concepts->add($concept);
        }
    }

    /**
     * @param MeshConceptInterface $concept
     */
    public function removeConcept(MeshConceptInterface $concept)
    {
        $this->concepts->removeElement($concept);
    }

    /**
    * @return MeshConceptInterface[]|ArrayCollection
    */
    public function getConcepts()
    {
        return $this->concepts;
    }
}
