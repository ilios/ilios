<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Entity\MeshConceptInterface;

/**
 * Interface ConceptsEntityInterface
 */
interface ConceptsEntityInterface
{
    /**
     * @param Collection $concepts
     */
    public function setConcepts(Collection $concepts);

    /**
     * @param MeshConceptInterface $concept
     */
    public function addConcept(MeshConceptInterface $concept);

    /**
     * @param MeshConceptInterface $concept
     */
    public function removeConcept(MeshConceptInterface $concept);

    /**
    * @return MeshConceptInterface[]|ArrayCollection
    */
    public function getConcepts();
}
