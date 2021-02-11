<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\AcademicYear;

class AcademicYearFactory
{
    protected Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function create(int $year): AcademicYear
    {
        $title = $this->config->get('academic_year_crosses_calendar_year_boundaries') ?
            sprintf("%d - %d", $year, $year + 1) : (string) $year;
        return new AcademicYear($year, $title);
    }
}
