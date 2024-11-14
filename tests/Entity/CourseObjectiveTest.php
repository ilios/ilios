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
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\CourseObjective::class)]
class CourseObjectiveTest extends EntityBase
{
    protected CourseObjective $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new CourseObjective();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testConstructor(): void
    {
        $this->assertEquals(0, $this->object->getPosition());
        $this->assertEquals(true, $this->object->isActive());
        $this->assertCount(0, $this->object->getMeshDescriptors());
        $this->assertCount(0, $this->object->getSessionObjectives());
        $this->assertCount(0, $this->object->getProgramYearObjectives());
        $this->assertCount(0, $this->object->getDescendants());
    }

    public function testNotBlankValidation(): void
    {
        $this->object->setCourse(new Course());
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
            'course',
        ];
        $this->validateNotNulls($notNull);

        $this->object->setCourse(new Course());
        $this->validate(0);
    }

    public function testSetTitle(): void
    {
        $title = 'foo';
        $this->object->setTitle($title);
        $this->assertEquals($title, $this->object->getTitle());
    }

    public function testSetCourse(): void
    {
        $this->entitySetTest('course', 'Course');
    }

    public function testSetPosition(): void
    {
        $position = 5;
        $this->object->setPosition(5);
        $this->assertEquals($position, $this->object->getPosition());
    }
    public function testAddTerm(): void
    {
        $this->entityCollectionAddTest('term', 'Term');
    }

    public function testRemoveTerm(): void
    {
        $this->entityCollectionRemoveTest('term', 'Term');
    }

    public function testSetTerms(): void
    {
        $this->entityCollectionSetTest('term', 'Term');
    }

    public function testAddMeshDescriptor(): void
    {
        $meshDescriptor = new MeshDescriptor();
        $this->assertCount(0, $this->object->getMeshDescriptors());
        $this->object->addMeshDescriptor($meshDescriptor);
        $this->assertEquals($meshDescriptor, $this->object->getMeshDescriptors()->first());
    }

    public function testRemoveMeshDescriptor(): void
    {
        $meshDescriptor = new MeshDescriptor();
        $this->assertCount(0, $this->object->getMeshDescriptors());
        $this->object->addMeshDescriptor($meshDescriptor);
        $this->assertEquals($meshDescriptor, $this->object->getMeshDescriptors()->first());
        $this->object->removeMeshDescriptor($meshDescriptor);
        $this->assertCount(0, $this->object->getMeshDescriptors());
    }

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

    public function testAddSessionObjective(): void
    {
        $sessionObjective = new SessionObjective();
        $this->assertCount(0, $this->object->getSessionObjectives());
        $this->object->addSessionObjective($sessionObjective);
        $this->assertCount(1, $this->object->getSessionObjectives());
        $this->assertEquals($sessionObjective, $this->object->getSessionObjectives()->first());
    }

    public function testRemoveSessionObjective(): void
    {
        $sessionObjective = new SessionObjective();
        $this->assertCount(0, $this->object->getSessionObjectives());
        $this->object->addSessionObjective($sessionObjective);
        $this->assertEquals($sessionObjective, $this->object->getSessionObjectives()->first());
        $this->object->removeSessionObjective($sessionObjective);
        $this->assertCount(0, $this->object->getSessionObjectives());
    }

    public function testGetSessionObjectives(): void
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

    public function testAddProgramYearObjective(): void
    {
        $pyObjective = new ProgramYearObjective();
        $this->assertCount(0, $this->object->getProgramYearObjectives());
        $this->object->addProgramYearObjective($pyObjective);
        $this->assertCount(1, $this->object->getProgramYearObjectives());
        $this->assertEquals($pyObjective, $this->object->getProgramYearObjectives()->first());
    }

    public function testRemoveProgramYearObjective(): void
    {
        $pyObjective = new ProgramYearObjective();
        $this->assertCount(0, $this->object->getProgramYearObjectives());
        $this->object->addProgramYearObjective($pyObjective);
        $this->assertEquals($pyObjective, $this->object->getProgramYearObjectives()->first());
        $this->object->removeProgramYearObjective($pyObjective);
        $this->assertCount(0, $this->object->getProgramYearObjectives());
    }

    public function testGetProgramYearObjectives(): void
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

    public function testSetAncestor(): void
    {
        $ancestor = new CourseObjective();
        $this->object->setAncestor($ancestor);
        $this->assertEquals($ancestor, $this->object->getAncestor());
    }

    public function testGetAncestorOrSelfWithAncestor(): void
    {
        $ancestor = new CourseObjective();
        $this->object->setAncestor($ancestor);
        $this->assertSame($ancestor, $this->object->getAncestorOrSelf());
    }

    public function testGetAncestorOrSelfWithNoAncestor(): void
    {
        $this->assertSame($this->object, $this->object->getAncestorOrSelf());
    }

    public function testAddDescendant(): void
    {
        $descendant = new CourseObjective();
        $this->assertCount(0, $this->object->getDescendants());
        $this->object->addDescendant($descendant);
        $this->assertEquals($descendant, $this->object->getDescendants()->first());
    }

    public function testRemoveDescendant(): void
    {
        $descendant = new CourseObjective();
        $this->assertCount(0, $this->object->getDescendants());
        $this->object->addDescendant($descendant);
        $this->assertEquals($descendant, $this->object->getDescendants()->first());
        $this->object->removeDescendant($descendant);
        $this->assertCount(0, $this->object->getDescendants());
    }

    public function testGetDescendants(): void
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

    public function testSetActive(): void
    {
        $this->assertTrue($this->object->isActive());
        $this->object->setActive(false);
        $this->assertFalse($this->object->isActive());
    }

    protected function getObject(): CourseObjective
    {
        return $this->object;
    }
}
