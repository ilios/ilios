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
    protected SessionObjective $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new SessionObjective();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    /**
     * @covers \App\Entity\SessionObjective::__construct
     */
    public function testConstructor(): void
    {
        $this->assertEquals(0, $this->object->getPosition());
        $this->assertEquals(true, $this->object->isActive());
        $this->assertCount(0, $this->object->getMeshDescriptors());
        $this->assertCount(0, $this->object->getCourseObjectives());
        $this->assertCount(0, $this->object->getDescendants());
    }

    public function testNotBlankValidation(): void
    {
        $this->object->setSession(new Session());
        $notBlank = [
            'title',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->validate(0);
    }

    public function testNotNullValidation(): void
    {
        $this->object->setTitle('foo');
        $notNull = [
            'session',
        ];
        $this->validateNotNulls($notNull);

        $this->object->setSession(new Session());
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\SessionObjective::setTitle
     * @covers \App\Entity\SessionObjective::getTitle
     */
    public function testSetTitle(): void
    {
        $title = 'foo';
        $this->object->setTitle($title);
        $this->assertEquals($title, $this->object->getTitle());
    }

    /**
     * @covers \App\Entity\SessionObjective::setSession
     * @covers \App\Entity\SessionObjective::getSession
     */
    public function testSetSession(): void
    {
        $this->entitySetTest('session', 'Session');
    }

    /**
     * @covers \App\Entity\SessionObjective::setPosition
     * @covers \App\Entity\SessionObjective::getPosition
     */
    public function testSetPosition(): void
    {
        $position = 5;
        $this->object->setPosition(5);
        $this->assertEquals($position, $this->object->getPosition());
    }
    /**
     * @covers \App\Entity\SessionObjective::addTerm
     */
    public function testAddTerm(): void
    {
        $this->entityCollectionAddTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\SessionObjective::removeTerm
     */
    public function testRemoveTerm(): void
    {
        $this->entityCollectionRemoveTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\SessionObjective::getTerms
     * @covers \App\Entity\SessionObjective::setTerms
     */
    public function testSetTerms(): void
    {
        $this->entityCollectionSetTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\SessionObjective::addMeshDescriptor
     */
    public function testAddMeshDescriptor(): void
    {
        $meshDescriptor = new MeshDescriptor();
        $this->assertCount(0, $this->object->getMeshDescriptors());
        $this->object->addMeshDescriptor($meshDescriptor);
        $this->assertEquals($meshDescriptor, $this->object->getMeshDescriptors()->first());
    }

    /**
     * @covers \App\Entity\SessionObjective::removeMeshDescriptor
     */
    public function testRemoveMeshDescriptor(): void
    {
        $meshDescriptor = new MeshDescriptor();
        $this->assertCount(0, $this->object->getMeshDescriptors());
        $this->object->addMeshDescriptor($meshDescriptor);
        $this->assertEquals($meshDescriptor, $this->object->getMeshDescriptors()->first());
        $this->object->removeMeshDescriptor($meshDescriptor);
        $this->assertCount(0, $this->object->getMeshDescriptors());
    }

    /**
     * @covers \App\Entity\SessionObjective::getMeshDescriptors
     */
    public function testGetMeshDescriptors(): void
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
    public function testAddCourseObjective(): void
    {
        $courseObjective = new CourseObjective();
        $this->assertCount(0, $this->object->getCourseObjectives());
        $this->object->addCourseObjective($courseObjective);
        $this->assertCount(1, $this->object->getCourseObjectives());

        $this->assertEquals($courseObjective, $this->object->getCourseObjectives()->first());
    }

    /**
     * @covers \App\Entity\SessionObjective::removeCourseObjective
     */
    public function testRemoveCourseObjective(): void
    {
        $courseObjective = new CourseObjective();
        $this->assertCount(0, $this->object->getCourseObjectives());
        $this->object->addCourseObjective($courseObjective);
        $this->assertEquals($courseObjective, $this->object->getCourseObjectives()->first());
        $this->object->removeCourseObjective($courseObjective);
        $this->assertCount(0, $this->object->getCourseObjectives());
    }

    /**
     * @covers \App\Entity\SessionObjective::getCourseObjectives
     * @covers \App\Entity\SessionObjective::setCourseObjectives
     */
    public function testGetCourseObjectives(): void
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
    public function testSetAncestor(): void
    {
        $ancestor = new SessionObjective();
        $this->object->setAncestor($ancestor);
        $this->assertEquals($ancestor, $this->object->getAncestor());
    }

    /**
     * @covers \App\Entity\SessionObjective::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithAncestor(): void
    {
        $ancestor = new SessionObjective();
        $this->object->setAncestor($ancestor);
        $this->assertSame($ancestor, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \App\Entity\SessionObjective::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithNoAncestor(): void
    {
        $this->assertSame($this->object, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \App\Entity\SessionObjective::addDescendant
     */
    public function testAddDescendant(): void
    {
        $descendant = new SessionObjective();
        $this->assertCount(0, $this->object->getDescendants());
        $this->object->addDescendant($descendant);
        $this->assertEquals($descendant, $this->object->getDescendants()->first());
    }

    /**
     * @covers \App\Entity\SessionObjective::removeDescendant
     */
    public function testRemoveDescendant(): void
    {
        $descendant = new SessionObjective();
        $this->assertCount(0, $this->object->getDescendants());
        $this->object->addDescendant($descendant);
        $this->assertEquals($descendant, $this->object->getDescendants()->first());
        $this->object->removeDescendant($descendant);
        $this->assertCount(0, $this->object->getDescendants());
    }

    /**
     * @covers \App\Entity\SessionObjective::getDescendants
     * @covers \App\Entity\SessionObjective::setDescendants
     */
    public function testGetDescendants(): void
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
    public function testSetActive(): void
    {
        $this->assertTrue($this->object->isActive());
        $this->object->setActive(false);
        $this->assertFalse($this->object->isActive());
    }

    protected function getObject(): SessionObjective
    {
        return $this->object;
    }
}
