<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\MeshConceptInterface;
use Doctrine\Common\Collections\Collection;

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
