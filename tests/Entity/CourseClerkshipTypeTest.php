<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\CourseClerkshipType;

/**
 * Tests for Entity CourseClerkshipType
 * @group model
 */
class CourseClerkshipTypeTest extends EntityBase
{
    protected CourseClerkshipType $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new CourseClerkshipType();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'title',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('20 max title');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\CourseClerkshipType::__construct
     */
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getCourses());
    }

    /**
     * @covers \App\Entity\CourseClerkshipType::setTitle
     * @covers \App\Entity\CourseClerkshipType::getTitle
     */
    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\CourseClerkshipType::addCourse
     */
    public function testAddCourse(): void
    {
        $this->entityCollectionAddTest('course', 'Course', false, false, 'setClerkshipType');
    }

    /**
     * @covers \App\Entity\CourseClerkshipType::removeCourse
     */
    public function testRemoveCourse(): void
    {
        $this->entityCollectionRemoveTest('course', 'Course');
    }

    /**
     * @covers \App\Entity\CourseClerkshipType::getCourses
     */
    public function testGetCourses(): void
    {
        $this->entityCollectionSetTest('course', 'Course', false, false, 'setClerkshipType');
    }

    protected function getObject(): CourseClerkshipType
    {
        return $this->object;
    }
}
