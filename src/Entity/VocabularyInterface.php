<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IndexableCoursesEntityInterface;
use App\Traits\ActivatableEntityInterface;
use App\Traits\CategorizableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\SchoolEntityInterface;
use App\Traits\TitledEntityInterface;
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
