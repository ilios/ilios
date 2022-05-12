<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IndexableCoursesEntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Traits\ActivatableEntityInterface;
use App\Traits\CategorizableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\SchoolEntityInterface;
use App\Traits\StringableEntityToIdInterface;
use App\Traits\TitledEntityInterface;

/**
 * Interface VocabularyInterface
 */
interface VocabularyInterface extends
    IdentifiableEntityInterface,
    SchoolEntityInterface,
    StringableEntityToIdInterface,
    TitledEntityInterface,
    CategorizableEntityInterface,
    ActivatableEntityInterface,
    IndexableCoursesEntityInterface
{
}
