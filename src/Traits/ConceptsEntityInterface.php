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
    public function setConcepts(Collection $concepts): void;

    public function addConcept(MeshConceptInterface $concept): void;

    public function removeConcept(MeshConceptInterface $concept): void;

    public function getConcepts(): Collection;
}
