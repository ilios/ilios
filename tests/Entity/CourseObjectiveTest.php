<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Course;
use App\Entity\CourseObjective;
use App\Entity\MeshDescriptor;
use App\Entity\ProgramYearObjective;
use App\Entity\SessionObjective;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @covers \App\Entity\CourseObjective::__construct
     */
    public function testConstructor()
    {
        $this->assertEquals(0, $this->object->getPosition());
        $this->assertEquals(true, $this->object->isActive());
        $this->assertEmpty($this->object->getMeshDescriptors());
        $this->assertEmpty($this->object->getSessionObjectives());
        $this->assertEmpty($this->object->getProgramYearObjectives());
        $this->assertEmpty($this->object->getDescendants());
    }

    public function testNotBlankValidation()
    {
        $this->object->setCourse(new Course());
        $notBlank = [
            'title'
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->validate(0);
    }

    public function testNotNullValidation()
    {
        $this->object->setTitle('foo');
        $notNull = [
            'course'
        ];
        $this->validateNotNulls($notNull);

        $this->object->setCourse(new Course());
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\CourseObjective::setTitle
     * @covers \App\Entity\CourseObjective::getTitle
     */
    public function testSetTitle()
    {
        $title = 'foo';
        $this->object->setTitle($title);
        $this->assertEquals($title, $this->object->getTitle());
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
        $position = 5;
        $this->object->setPosition(5);
        $this->assertEquals($position, $this->object->getPosition());
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

    /**
     * @covers \App\Entity\CourseObjective::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $meshDescriptor = new MeshDescriptor();
        $this->assertEmpty($this->object->getMeshDescriptors());
        $this->object->addMeshDescriptor($meshDescriptor);
        $this->assertEquals($meshDescriptor, $this->object->getMeshDescriptors()->first());
    }

    /**
     * @covers \App\Entity\CourseObjective::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor()
    {
        $meshDescriptor = new MeshDescriptor();
        $this->assertEmpty($this->object->getMeshDescriptors());
        $this->object->addMeshDescriptor($meshDescriptor);
        $this->assertEquals($meshDescriptor, $this->object->getMeshDescriptors()->first());
        $this->object->removeMeshDescriptor($meshDescriptor);
        $this->assertEmpty($this->object->getMeshDescriptors());
    }

    /**
     * @covers \App\Entity\CourseObjective::getMeshDescriptors
     */
    public function testGetMeshDescriptors()
    {
        $meshDescriptors = [];
        for ($i = 0; $i < 10; $i++) {
            $meshDescriptors[] = new MeshDescriptor();
        }
        $this->object->setMeshDescriptors(new ArrayCollection($meshDescriptors));
        $returnedMeshDescriptors = $this->object->getMeshDescriptors();
        foreach ($meshDescriptors as $meshDescriptor) {
            $this->assertTrue($returnedMeshDescriptors->contains($meshDescriptor));
        }
    }

    /**
     * @covers \App\Entity\CourseObjective::addSessionObjective
     */
    public function testAddSessionObjective()
    {
        $sessionObjective = new SessionObjective();
        $this->assertEmpty($this->object->getSessionObjectives());
        $this->object->addSessionObjective($sessionObjective);
        $this->assertCount(1, $this->object->getSessionObjectives());
        $this->assertEquals($sessionObjective, $this->object->getSessionObjectives()->first());
    }

    /**
     * @covers \App\Entity\CourseObjective::removeSessionObjective
     */
    public function testRemoveSessionObjective()
    {
        $sessionObjective = new SessionObjective();
        $this->assertEmpty($this->object->getSessionObjectives());
        $this->object->addSessionObjective($sessionObjective);
        $this->assertEquals($sessionObjective, $this->object->getSessionObjectives()->first());
        $this->object->removeSessionObjective($sessionObjective);
        $this->assertEmpty($this->object->getSessionObjectives());
    }

    /**
     * @covers \App\Entity\CourseObjective::getSessionObjectives
     * @covers \App\Entity\CourseObjective::setSessionObjectives
     */
    public function testGetSessionObjectives()
    {
        $sessionObjectives = [];
        for ($i = 0; $i < 10; $i++) {
            $sessionObjectives[] = new SessionObjective();
        }
        $this->object->setSessionObjectives(new ArrayCollection($sessionObjectives));
        $returnedSessionObjectives = $this->object->getSessionObjectives();
        foreach ($sessionObjectives as $sessionObjective) {
            $this->assertTrue($returnedSessionObjectives->contains($sessionObjective));
        }
    }

    /**
     * @covers \App\Entity\CourseObjective::addProgramYearObjective
     */
    public function testAddProgramYearObjective()
    {
        $pyObjective = new ProgramYearObjective();
        $this->assertEmpty($this->object->getProgramYearObjectives());
        $this->object->addProgramYearObjective($pyObjective);
        $this->assertCount(1, $this->object->getProgramYearObjectives());
        $this->assertEquals($pyObjective, $this->object->getProgramYearObjectives()->first());
    }

    /**
     * @covers \App\Entity\CourseObjective::removeProgramYearObjective
     */
    public function testRemoveProgramYearObjective()
    {
        $pyObjective = new ProgramYearObjective();
        $this->assertEmpty($this->object->getProgramYearObjectives());
        $this->object->addProgramYearObjective($pyObjective);
        $this->assertEquals($pyObjective, $this->object->getProgramYearObjectives()->first());
        $this->object->removeProgramYearObjective($pyObjective);
        $this->assertEmpty($this->object->getProgramYearObjectives());
    }

    /**
     * @covers \App\Entity\CourseObjective::getProgramYearObjectives
     * @covers \App\Entity\CourseObjective::setProgramYearObjectives
     */
    public function testGetProgramYearObjectives()
    {
        $pyObjectives = [];
        for ($i = 0; $i < 10; $i++) {
            $pyObjectives[] = new ProgramYearObjective();
        }
        $this->object->setProgramYearObjectives(new ArrayCollection($pyObjectives));
        $returnedPyObjectives = $this->object->getProgramYearObjectives();
        foreach ($pyObjectives as $pyObjective) {
            $this->assertTrue($returnedPyObjectives->contains($pyObjective));
        }
    }

    /**
     * @covers \App\Entity\CourseObjective::setAncestor
     * @covers \App\Entity\CourseObjective::getAncestor
     */
    public function testSetAncestor()
    {
        $ancestor = new CourseObjective();
        $this->object->setAncestor($ancestor);
        $this->assertEquals($ancestor, $this->object->getAncestor());
    }

    /**
     * @covers \App\Entity\CourseObjective::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithAncestor()
    {
        $ancestor = new CourseObjective();
        $this->object->setAncestor($ancestor);
        $this->assertSame($ancestor, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \App\Entity\CourseObjective::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithNoAncestor()
    {
        $this->assertSame($this->object, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \App\Entity\CourseObjective::addDescendant
     */
    public function testAddDescendant()
    {
        $descendant = new CourseObjective();
        $this->assertEmpty($this->object->getDescendants());
        $this->object->addDescendant($descendant);
        $this->assertEquals($descendant, $this->object->getDescendants()->first());
    }

    /**
     * @covers \App\Entity\CourseObjective::removeDescendant
     */
    public function testRemoveDescendant()
    {
        $descendant = new CourseObjective();
        $this->assertEmpty($this->object->getDescendants());
        $this->object->addDescendant($descendant);
        $this->assertEquals($descendant, $this->object->getDescendants()->first());
        $this->object->removeDescendant($descendant);
        $this->assertEmpty($this->object->getDescendants());
    }

    /**
     * @covers \App\Entity\CourseObjective::getDescendants
     * @covers \App\Entity\CourseObjective::setDescendants
     */
    public function testGetDescendants()
    {
        $descendants = [];
        for ($i = 0; $i < 10; $i++) {
            $descendants[] = new CourseObjective();
        }
        $this->object->setDescendants(new ArrayCollection($descendants));
        $returnedDescendants = $this->object->getDescendants();
        foreach ($descendants as $descendant) {
            $this->assertTrue($returnedDescendants->contains($descendant));
        }
    }

    /**
     * @covers \App\Entity\CourseObjective::setActive
     * @covers \App\Entity\CourseObjective::isActive
     */
    public function testSetActive()
    {
        $this->assertTrue($this->object->isActive());
        $this->object->setActive(false);
        $this->assertFalse($this->object->isActive());
    }
}
