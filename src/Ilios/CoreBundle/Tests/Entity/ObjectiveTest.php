<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\Objective;
use Mockery as m;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tests for Entity Objective
 */
class ObjectiveTest extends EntityBase
{
    /**
     * @var Objective
     */
    protected $object;

    /**
     * Instantiate a Objective object
     */
    protected function setUp()
    {
        $this->object = new Objective;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'title'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->validate(0);
    }
    /**
     * @covers Ilios\CoreBundle\Entity\Objective::setTitle
     * @covers Ilios\CoreBundle\Entity\Objective::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::setCompetency
     * @covers Ilios\CoreBundle\Entity\Objective::getCompetency
     */
    public function testSetCompetency()
    {
        $this->entitySetTest('competency', 'Competency');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::addCourse
     */
    public function testAddCourse()
    {
        $goodCourse = m::mock('Ilios\CoreBundle\Entity\Course')
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(false)
            ->mock();
        $deletedCourse = m::mock('Ilios\CoreBundle\Entity\Course')
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(true)
            ->mock();
        $this->object->addCourse($goodCourse);
        $this->object->addCourse($deletedCourse);
        $results = $this->object->getCourses();
        $this->assertTrue($results instanceof ArrayCollection, 'Collection not returned.');

        $this->assertTrue($results->contains($goodCourse));
        $this->assertFalse($results->contains($deletedCourse));
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::getCourses
     */
    public function testGetCourses()
    {
        $goodCourse = m::mock('Ilios\CoreBundle\Entity\Course')
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(false)
            ->mock();
        $deletedCourse = m::mock('Ilios\CoreBundle\Entity\Course')
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(true)
            ->mock();
        $collection = new ArrayCollection([$goodCourse, $deletedCourse]);
        $this->object->setCourses($collection);
        $results = $this->object->getCourses();
        $this->assertTrue($results instanceof ArrayCollection, 'Collection not returned.');

        $this->assertTrue($results->contains($goodCourse));
        $this->assertFalse($results->contains($deletedCourse));
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::addSession
     */
    public function testAddSession()
    {
        $goodSession = m::mock('Ilios\CoreBundle\Entity\Session')
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(false)
            ->mock();
        $deletedSession = m::mock('Ilios\CoreBundle\Entity\Session')
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(true)
            ->mock();
        $this->object->addSession($goodSession);
        $this->object->addSession($deletedSession);
        $results = $this->object->getSessions();
        $this->assertTrue($results instanceof ArrayCollection, 'Collection not returned.');

        $this->assertTrue($results->contains($goodSession));
        $this->assertFalse($results->contains($deletedSession));
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::getSessions
     */
    public function testGetSessions()
    {
        $goodSession = m::mock('Ilios\CoreBundle\Entity\Session')
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(false)
            ->mock();
        $deletedSession = m::mock('Ilios\CoreBundle\Entity\Session')
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(true)
            ->mock();
        $collection = new ArrayCollection([$goodSession, $deletedSession]);
        $this->object->setSessions($collection);
        $results = $this->object->getSessions();
        $this->assertTrue($results instanceof ArrayCollection, 'Collection not returned.');

        $this->assertTrue($results->contains($goodSession));
        $this->assertFalse($results->contains($deletedSession));
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::addChild
     */
    public function testAddChild()
    {
        $this->entityCollectionAddTest('children', 'Objective', 'getChildren', 'addChild');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::getChildren
     */
    public function testGetChildren()
    {
        $this->entityCollectionSetTest('children', 'Objective', 'getChildren', 'setChildren', false);
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::addMeshDescriptor
     */
    public function testAddMeshDescriptor()
    {
        $this->entityCollectionAddTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::getMeshDescriptors
     */
    public function testGetMeshDescriptors()
    {
        $this->entityCollectionSetTest('meshDescriptor', 'MeshDescriptor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::addParent
     */
    public function testAddParent()
    {
        $this->entityCollectionAddTest('parent', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Objective::getParents
     */
    public function testGetParents()
    {
        $this->entityCollectionSetTest('parent', 'Objective');
    }
}
