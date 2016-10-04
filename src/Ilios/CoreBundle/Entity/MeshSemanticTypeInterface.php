<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\NameableEntityInterface;
use Ilios\CoreBundle\Traits\TimestampableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

/**
 * Interface MeshSemanticTypeInterface
 * @package Ilios\CoreBundle\Entity
 * @deprecated
 */
interface MeshSemanticTypeInterface extends
    IdentifiableEntityInterface,
    NameableEntityInterface,
    TimestampableEntityInterface
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
     * @return ArrayCollection|MeshConceptInterface[]
     */
    public function getConcepts();
}
