<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Course;
use App\Traits\CoursesEntity;
use Mockery as m;
use App\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversTrait;

#[CoversTrait(CoursesEntity::class)]
final class CoursesEntityTest extends TestCase
{
    private object $traitObject;
    public function setUp(): void
    {
        parent::setUp();
        $this->traitObject = new class {
            use CoursesEntity;
        };
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->traitObject);
    }

    public function testSetCourses(): void
    {
        $collection = new ArrayCollection();
        $collection->add(m::mock(Course::class));
        $collection->add(m::mock(Course::class));
        $collection->add(m::mock(Course::class));

        $this->traitObject->setCourses($collection);
        $this->assertEquals($collection, $this->traitObject->getCourses());
    }

    public function testRemoveCourse(): void
    {
        $collection = new ArrayCollection();
        $one = m::mock(Course::class);
        $two = m::mock(Course::class);
        $collection->add($one);
        $collection->add($two);

        $this->traitObject->setCourses($collection);
        $this->traitObject->removeCourse($one);
        $courses = $this->traitObject->getCourses();
        $this->assertEquals(1, $courses->count());
        $this->assertEquals($two, $courses->first());
    }

    public function testAddCourse(): void
    {
        $this->traitObject->setCourses(new ArrayCollection());
        $this->assertCount(0, $this->traitObject->getCourses());

        $one = m::mock(Course::class);
        $this->traitObject->addCourse($one);
        $this->assertCount(1, $this->traitObject->getCourses());
        $this->assertEquals($one, $this->traitObject->getCourses()->first());
        // duplicate prevention check
        $this->traitObject->addCourse($one);
        $this->assertCount(1, $this->traitObject->getCourses());
        $this->assertEquals($one, $this->traitObject->getCourses()->first());

        $two = m::mock(Course::class);
        $this->traitObject->addCourse($two);
        $this->assertCount(2, $this->traitObject->getCourses());
        $this->assertEquals($one, $this->traitObject->getCourses()->first());
        $this->assertEquals($two, $this->traitObject->getCourses()->last());
    }
}
