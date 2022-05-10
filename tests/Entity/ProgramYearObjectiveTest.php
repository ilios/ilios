<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Competency;
use App\Entity\CourseObjective;
use App\Entity\MeshDescriptor;
use App\Entity\ProgramYear;
use App\Entity\ProgramYearObjective;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tests for Entity ProgramYearObjective
 * @group model
 */
class ProgramYearObjectiveTest extends EntityBase
{
    /**
     * @var ProgramYearObjective
     */
    protected $object;

    /**
     * Instantiate a ProgramYearObjective object
     */
    protected function setUp(): void
    {
        $this->object = new ProgramYearObjective();
    }

    /**
     * @covers \App\Entity\ProgramYearObjective::__construct
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
        $this->object->setProgramYear(new ProgramYear());
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
            'programYear'
        ];
        $this->validateNotNulls($notNull);

        $this->object->setProgramYear(new ProgramYear());
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\ProgramYearObjective::setTitle
     * @covers \App\Entity\ProgramYearObjective::getTitle
     */
    public function testSetTitle()
    {
        $title = 'foo';
        $this->object->setTitle($title);
        $this->assertEquals($title, $this->object->getTitle());
    }

    /**
     * @covers \App\Entity\ProgramYearObjective::setProgramYear
     * @covers \App\Entity\ProgramYearObjective::getProgramYear
     */
    public function testSetProgramYear()
    {
        $this->entitySetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers \App\Entity\ProgramYearObjective::setPosition
     * @covers \App\Entity\ProgramYearObjective::getPosition
     */
    public function testSetPosition()
    {
        $position = 5;
        $this->object->setPosition(5);
        $this->assertEquals($position, $this->object->getPosition());
    }
    /**
     * @covers \App\Entity\ProgramYearObjective::addTerm
     */
    public function testAddTerm()
    {
        $this->entityCollectionAddTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\ProgramYearObjective::removeTerm
     */
    public function testRemoveTerm()
    {
        $this->entityCollectionRemoveTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\ProgramYearObjective::getTerms
     * @covers \App\Entity\ProgramYearObjective::setTerms
     */
    public function testSetTerms()
    {
        $this->entityCollectionSetTest('term', 'Term');
    }

    /**
     * @covers \App\Entity\ProgramYearObjective::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $meshDescriptor = new MeshDescriptor();
        $this->assertEmpty($this->object->getMeshDescriptors());
        $this->object->addMeshDescriptor($meshDescriptor);
        $this->assertEquals($meshDescriptor, $this->object->getMeshDescriptors()->first());
    }

    /**
     * @covers \App\Entity\ProgramYearObjective::removeMeshDescriptor
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
     * @covers \App\Entity\ProgramYearObjective::getMeshDescriptors
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
     * @covers \App\Entity\ProgramYearObjective::addCourseObjective
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
     * @covers \App\Entity\ProgramYearObjective::removeCourseObjective
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
     * @covers \App\Entity\ProgramYearObjective::getCourseObjectives
     * @covers \App\Entity\ProgramYearObjective::setCourseObjectives
     */
    public function getGetCourseObjectives()
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
     * @covers \App\Entity\ProgramYearObjective::setCompetency
     * @covers \App\Entity\ProgramYearObjective::getCompetency
     */
    public function testSetCompetency()
    {
        $competency = new Competency();
        $this->assertNull($this->object->getCompetency());
        $this->object->setCompetency($competency);
        $this->assertEquals($competency, $this->object->getCompetency());
    }

    /**
     * @covers \App\Entity\ProgramYearObjective::setAncestor
     * @covers \App\Entity\ProgramYearObjective::getAncestor
     */
    public function testSetAncestor()
    {
        $ancestor = new ProgramYearObjective();
        $this->object->setAncestor($ancestor);
        $this->assertEquals($ancestor, $this->object->getAncestor());
    }

    /**
     * @covers \App\Entity\ProgramYearObjective::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithAncestor()
    {
        $ancestor = new ProgramYearObjective();
        $this->object->setAncestor($ancestor);
        $this->assertSame($ancestor, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \App\Entity\ProgramYearObjective::getAncestorOrSelf
     */
    public function testGetAncestorOrSelfWithNoAncestor()
    {
        $this->assertSame($this->object, $this->object->getAncestorOrSelf());
    }

    /**
     * @covers \App\Entity\ProgramYearObjective::addDescendant
     */
    public function testAddDescendant()
    {
        $descendant = new ProgramYearObjective();
        $this->assertEmpty($this->object->getDescendants());
        $this->object->addDescendant($descendant);
        $this->assertEquals($descendant, $this->object->getDescendants()->first());
    }

    /**
     * @covers \App\Entity\ProgramYearObjective::removeDescendant
     */
    public function testRemoveDescendant()
    {
        $descendant = new ProgramYearObjective();
        $this->assertEmpty($this->object->getDescendants());
        $this->object->addDescendant($descendant);
        $this->assertEquals($descendant, $this->object->getDescendants()->first());
        $this->object->removeDescendant($descendant);
        $this->assertEmpty($this->object->getDescendants());
    }

    /**
     * @covers \App\Entity\ProgramYearObjective::getDescendants
     * @covers \App\Entity\ProgramYearObjective::setDescendants
     */
    public function testGetDescendants()
    {
        $descendants = [];
        for ($i = 0; $i < 10; $i++) {
            $descendants[] = new ProgramYearObjective();
        }
        $this->object->setDescendants(new ArrayCollection($descendants));
        $returnedDescendants = $this->object->getDescendants();
        foreach ($descendants as $descendant) {
            $this->assertTrue($returnedDescendants->contains($descendant));
        }
    }

    /**
     * @covers \App\Entity\ProgramYearObjective::setActive
     * @covers \App\Entity\ProgramYearObjective::isActive
     */
    public function testSetActive()
    {
        $this->assertTrue($this->object->isActive());
        $this->object->setActive(false);
        $this->assertFalse($this->object->isActive());
    }
}
