<?php

declare(strict_types=1);

namespace App\Tests\Classes;

use App\Classes\AcademicYear;
use App\Tests\TestCase;

final class AcademicYearTest extends TestCase
{
    public function testConstructor(): void
    {
        $obj = new AcademicYear(2021, '2021 - 2022');
        $this->assertSame($obj->id, 2021);
        $this->assertSame($obj->title, '2021 - 2022');
    }
}
