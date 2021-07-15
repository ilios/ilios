<?php

declare(strict_types=1);

namespace App\Classes;

use App\Attribute as IA;

/**
 * Class AcademicYear
 */
#[IA\DTO('academicYears')]
class AcademicYear
{
    /**
     */
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('string')]
    public int $id;
    /**
     */
    #[IA\Expose]
    #[IA\Type('string')]
    public string $title;
    public function __construct(int $year, string $title)
    {
        $this->id = $year;
        $this->title = $title;
    }
}
