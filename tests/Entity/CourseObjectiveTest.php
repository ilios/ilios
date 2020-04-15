<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\CourseObjective;

/**
 * Tests for Entity CourseObjective
 * @group model
 */
class CourseObjectiveTest extends EntityBase
{
    /**
     * @var CourseObjective
     */
    protected $object;

    /**
     * Instantiate a CourseObjective object
     */
    protected function setUp(): void
    {
        $this->object = new CourseObjective();
    }

    /**
     * @covers \App\Entity\CourseObjective::setCourse
     * @covers \App\Entity\CourseObjective::getCourse
     */
    public function testSetCourse()
    {
        $this->entitySetTest('course', 'Course');
    }

    /**
     * @covers \App\Entity\CourseObjective::setPosition
     * @covers \App\Entity\CourseObjective::getPosition
     */
    public function testSetPosition()
    {
        $this->basicSetTest('position', 'integer');
    }
    /**
     * @covers \App\Entity\CourseObjective::addTerm
     */
    public function testAddTerm()
    {
        $this->entityCollectionAddTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\CourseObjective::removeTerm
     */
    public function testRemoveTerm()
    {
        $this->entityCollectionRemoveTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\CourseObjective::getTerms
     * @covers \App\Entity\CourseObjective::setTerms
     */
    public function testSetTerms()
    {
        $this->entityCollectionSetTest('term', 'Term');
    }
}
