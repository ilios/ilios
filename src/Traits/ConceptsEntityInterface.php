<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\MeshConceptInterface;

/**
 * Interface ConceptsEntityInterface
 */
interface ConceptsEntityInterface
{
    public function setConcepts(Collection $concepts);

    public function addConcept(MeshConceptInterface $concept);

    public function removeConcept(MeshConceptInterface $concept);

    /**
    * @return MeshConceptInterface[]|ArrayCollection
    */
    public function getConcepts();
}
