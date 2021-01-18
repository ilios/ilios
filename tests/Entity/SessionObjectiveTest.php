<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\CourseObjective;
use App\Entity\MeshDescriptor;
use App\Entity\Session;
use App\Entity\SessionObjective;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tests for Entity SessionObjective
 * @group model
 */
class SessionObjectiveTest extends EntityBase
{
    /**
     * @var SessionObjective
     */
    protected $object;

    /**
     * Instantiate a SessionObjective object
     */
    protected function setUp(): void
    {
        $this->object = new SessionObjective();
    }

    /**
     * @covers \App\Entity\SessionObjective::__construct
     */
    public function testConstructor()
    {
        $this->assertEquals(0, $this->object->getPosition());
        $this->assertEquals(true, $this->object->isActive());
        $this->assertEmpty($this->object->getMeshDescriptors());
        $this->assertEmpty($this->object->getCourseObjectives());
        $this->assertEmpty($this->object->getDescendants());
    }

    public function testNotBlankValidation()
    {
        $this->object->setSession(new Session());
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
            'session'
        ];
        $this->validateNotNulls($notNull);

        $this->object->setSession(new Session());
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\SessionObjective::setTitle
     * @covers \App\Entity\SessionObjective::getTitle
     */
    public function testSetTitle()
    {
        $title = 'foo';
        $this->object->setTitle($title);
        $this->assertEquals($title, $this->object->getTitle());
    }

    /**
     * @covers \App\Entity\SessionObjective::setSession
     * @covers \App\Entity\SessionObjective::getSession
     */
    public function testSetSession()
    {
        $this->entitySetTest('session', 'Session');
    }

    /**
     * @covers \App\Entity\SessionObjective::setPosition
     * @covers \App\Entity\SessionObjective::getPosition
     */
    public function testSetPosition()
    {
        $position = 5;
        $this->object->setPosition(5);
        $this->assertEquals($position, $this->object->getPosition());
    }
    /**
     * @covers \App\Entity\SessionObjective::addTerm
     */
    public function testAddTerm()
    {
        $this->entityCollectionAddTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\SessionObjective::removeTerm
     */
    public function testRemoveTerm()
    {
        $this->entityCollectionRemoveTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\SessionObjective::getTerms
     * @covers \App\Entity\SessionObjective::setTerms
     */
    public function testSetTerms()
    {
        $this->entityCollectionSetTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\SessionObjective::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $meshDescriptor = new MeshDescriptor();
        $this->assertEmpty($this->object->getMeshDescriptors());
        $this->object->addMeshDescriptor($meshDescriptor);
        $this->assertEquals($meshDescriptor, $this->object->getMeshDescriptors()->first());
    }

    /**
     * @covers \App\Entity\SessionObjective::removeMeshDescriptor
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
     * @covers \App\Entity\SessionObjective::getMeshDescriptors
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
     * @covers \App\Entity\SessionObjective::addCourseObjective
     */
    public function testAddCourseObjective()
    {
        $courseObjective = new CourseObjective();
        $this->assertEmpty($this->object->getCourseObjectives());
        $this->object->addCourseObjective($courseObjective);
        $this->assertCount(1, $this->object->getCourseObjectives());

        $this->assertEquals($courseObjective, $this->object->getCourseObjectives()->first());
    }

    /**
     * @covers \App\Entity\SessionObjective::removeCourseObjective
     */
    public function testRemoveCourseObjective()
    {
        $courseObjective = new CourseObjective();
        $this->assertEmpty($this->object->getCourseObjectives());
        $this->object->addCourseObjective($courseObjective);
        $this->assertEquals($courseObjective, $this->object->getCourseObjectives()->first());
        $this->object->removeCourseObjective($courseObjective);
        $this->assertEmpty($this->object->getCourseObjectives());
    }

    /**
     * @covers \App\Entity\SessionObjective::getCourseObjectives
     * @covers \App\Entity\SessionObjective::setCourseObjectives
     */
    public function testGetCourseObjectives()
    {
        $courseObjectives = [];
        for ($i = 0; $i < 10; $i++) {
            $courseObjectives[] = new CourseObjective();
        }
        $this->object->setCourseObjectives(new ArrayCollection($courseObjectives));
        $returnedCourseObjectives = $this->object->getCourseObjectives();
        foreach ($courseObjectives as $courseObjective) {
            $this->assertTrue($returnedCourseObjectives->contains($courseObjective));
        }
    }

    /**
     * @covers \App\Entity\SessionObjective::setAncestor
     * @covers \App\Entity\SessionObjective::getAncestor
     */
    public function testSetAncestor()
    {
        $ancestor = new SessionObjective();
        $this->object->setAncestor($ancestor);
        $this->assertEquals($ancestor, $this->object->getAncestor());
    }

    /**
     * @covers \App\Entity\SessionObjective::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithAncestor()
    {
        $ancestor = new SessionObjective();
        $this->object->setAncestor($ancestor);
        $this->assertSame($ancestor, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \App\Entity\SessionObjective::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithNoAncestor()
    {
        $this->assertSame($this->object, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \App\Entity\SessionObjective::addDescendant
     */
    public function testAddDescendant()
    {
        $descendant = new SessionObjective();
        $this->assertEmpty($this->object->getDescendants());
        $this->object->addDescendant($descendant);
        $this->assertEquals($descendant, $this->object->getDescendants()->first());
    }

    /**
     * @covers \App\Entity\SessionObjective::removeDescendant
     */
    public function testRemoveDescendant()
    {
        $descendant = new SessionObjective();
        $this->assertEmpty($this->object->getDescendants());
        $this->object->addDescendant($descendant);
        $this->assertEquals($descendant, $this->object->getDescendants()->first());
        $this->object->removeDescendant($descendant);
        $this->assertEmpty($this->object->getDescendants());
    }

    /**
     * @covers \App\Entity\SessionObjective::getDescendants
     * @covers \App\Entity\SessionObjective::setDescendants
     */
    public function testGetDescendants()
    {
        $descendants = [];
        for ($i = 0; $i < 10; $i++) {
            $descendants[] = new SessionObjective();
        }
        $this->object->setDescendants(new ArrayCollection($descendants));
        $returnedDescendants = $this->object->getDescendants();
        foreach ($descendants as $descendant) {
            $this->assertTrue($returnedDescendants->contains($descendant));
        }
    }

    /**
     * @covers \App\Entity\SessionObjective::setActive
     * @covers \App\Entity\SessionObjective::isActive
     */
    public function testSetActive()
    {
        $this->assertTrue($this->object->isActive());
        $this->object->setActive(false);
        $this->assertFalse($this->object->isActive());
    }
}
