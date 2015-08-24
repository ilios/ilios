<?php
namespace Ilios\CoreBundle\Tests\Classes;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

use Ilios\CoreBundle\Classes\AcademicYear;

class AcademicYearTest extends TestCase
{

    public function testConstructorSetsId()
    {
        $obj = new AcademicYear(15);
        $this->assertSame($obj->getId(), 15);
    }

    public function testConstructorSetsTitle()
    {
        $obj = new AcademicYear(15);
        $this->assertSame($obj->getTitle(), 15);
    }
}
