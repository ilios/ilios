<?php

declare(strict_types=1);

namespace App\Tests\Classes;

use App\Classes\AcademicYear;
use App\Tests\TestCase;

class AcademicYearTest extends TestCase
{

    public function testConstructorSetsId()
    {
        $obj = new AcademicYear(15);
        $this->assertSame($obj->id, 15);
    }

    public function testConstructorSetsTitle()
    {
        $obj = new AcademicYear(15);
        $this->assertSame($obj->title, 15);
    }
}
