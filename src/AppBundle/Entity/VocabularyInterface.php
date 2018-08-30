<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use AppBundle\Traits\ActivatableEntityInterface;
use AppBundle\Traits\CategorizableEntityInterface;
use AppBundle\Traits\IdentifiableEntityInterface;
use AppBundle\Traits\SchoolEntityInterface;
use AppBundle\Traits\StringableEntityInterface;
use AppBundle\Traits\TitledEntityInterface;

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
