<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\IndexableCoursesEntityInterface;
use App\Entity\ActivatableEntityInterface;
use App\Entity\CategorizableEntityInterface;
use App\Entity\IdentifiableEntityInterface;
use App\Entity\SchoolEntityInterface;
use App\Entity\TitledEntityInterface;
use Stringable;

interface VocabularyInterface extends
    IdentifiableEntityInterface,
    SchoolEntityInterface,
    TitledEntityInterface,
    CategorizableEntityInterface,
    ActivatableEntityInterface,
    IndexableCoursesEntityInterface,
    Stringable
{
}
