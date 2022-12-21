<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IndexableCoursesEntityInterface;
use App\Traits\ActivatableEntityInterface;
use App\Traits\CategorizableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\SchoolEntityInterface;
use App\Traits\StringableEntityInterface;
use App\Traits\TitledEntityInterface;

interface VocabularyInterface extends
    IdentifiableEntityInterface,
    SchoolEntityInterface,
    StringableEntityInterface,
    TitledEntityInterface,
    CategorizableEntityInterface,
    ActivatableEntityInterface,
    IndexableCoursesEntityInterface
{
}
