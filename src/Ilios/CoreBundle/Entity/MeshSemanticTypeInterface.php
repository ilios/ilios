<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\ConceptsEntityInterface;
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
    TimestampableEntityInterface,
    ConceptsEntityInterface
{
}
