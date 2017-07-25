<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\ActivatableEntityInterface;
use Ilios\CoreBundle\Traits\CategorizableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\SchoolEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;

/**
 * Interface VocabularyInterface
 */
interface VocabularyInterface extends
    IdentifiableEntityInterface,
    SchoolEntityInterface,
    StringableEntityInterface,
    TitledEntityInterface,
    CategorizableEntityInterface,
    ActivatableEntityInterface
{
}
