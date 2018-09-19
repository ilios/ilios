<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\ActivatableEntityInterface;
use App\Traits\CategorizableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\SchoolEntityInterface;
use App\Traits\StringableEntityInterface;
use App\Traits\TitledEntityInterface;

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
