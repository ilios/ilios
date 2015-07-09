<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\MeshDescriptor;
use Mockery as m;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tests for Entity MeshDescriptor
 */
class MeshDescriptorTest extends EntityBase
{
    /**
     * @var MeshDescriptor
     */
    protected $object;

    /**
     * Instantiate a MeshDescriptor object
     */
    protected function setUp()
    {
        $this->object = new MeshDescriptor;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'name'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setName('test name');
        $this->validate(0);
    }
    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCourses());
        $this->assertEmpty($this->object->getCourseLearningMaterials());
        $this->assertEmpty($this->object->getObjectives());
        $this->assertEmpty($this->object->getSessions());
        $this->assertEmpty($this->object->getSessionLearningMaterials());
        $now = new \DateTime();
        $createdAt = $this->object->getCreatedAt();
        $this->assertTrue($createdAt instanceof \DateTime);
        $diff = $now->diff($createdAt);
        $this->assertTrue($diff->s < 2);
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::setName
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::setAnnotation
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::getAnnotation
     */
    public function testSetAnnotation()
    {
        $this->basicSetTest('annotation', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::addCourse
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
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::getCourses
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
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::addObjective
     */
    public function testAddObjective()
    {
        $this->entityCollectionAddTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::getObjectives
     */
    public function testGetObjectives()
    {
        $this->entityCollectionSetTest('objective', 'Objective');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::addSession
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
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::getSessions
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
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::addSessionLearningMaterial
     */
    public function testAddSessionLearningMaterial()
    {
        $this->entityCollectionAddTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::getSessionLearningMaterials
     */
    public function testGetSessionLearningMaterials()
    {
        $this->entityCollectionSetTest('sessionLearningMaterial', 'SessionLearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::addCourseLearningMaterial
     */
    public function testAddCourseLearningMaterial()
    {
        $this->entityCollectionAddTest('courseLearningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::getCourseLearningMaterials
     */
    public function testGetCourseLearningMaterials()
    {
        $this->entityCollectionSetTest('courseLearningMaterial', 'CourseLearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\MeshDescriptor::stampUpdate
     */
    public function testStampUpdate()
    {
        $now = new \DateTime();
        $this->object->stampUpdate();
        $updatedAt = $this->object->getUpdatedAt();
        $this->assertTrue($updatedAt instanceof \DateTime);
        $diff = $now->diff($updatedAt);
        $this->assertTrue($diff->s < 2);

    }
}
