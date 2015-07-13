<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\IlmSession;
use Mockery as m;

/**
 * Tests for Entity IlmSession
 */
class IlmSessionTest extends EntityBase
{
    /**
     * @var IlmSession
     */
    protected $object;

    /**
     * Instantiate a IlmSession object
     */
    protected function setUp()
    {
        $this->object = new IlmSession;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'hours',
            'dueDate'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setHours(55);
        $this->object->setDueDate(new \DateTime());
        $this->validate(0);
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getLearnerGroups());
        $this->assertEmpty($this->object->getInstructors());
        $this->assertEmpty($this->object->getInstructorGroups());
        $this->assertEmpty($this->object->getLearners());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::setHours
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::getHours
     */
    public function testSetHours()
    {
        $this->basicSetTest('hours', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::setDueDate
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::getDueDate
     */
    public function testSetDueDate()
    {
        $this->basicSetTest('dueDate', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::addLearnerGroup
     */
    public function testAddLearnerGroup()
    {
        $this->entityCollectionAddTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::getLearnerGroups
     */
    public function testGetLearnerGroups()
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::getInstructorGroups
     */
    public function testGetInstructorGroups()
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::addInstructor
     */
    public function testAddInstructor()
    {
        $this->entityCollectionAddTest('instructor', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::getInstructors
     */
    public function testGetInstructors()
    {
        $this->entityCollectionSetTest('instructor', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::addLearner
     */
    public function testAddLearner()
    {
        $this->entityCollectionAddTest('learner', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\IlmSessionFacet::getLearners
     */
    public function testGetLearners()
    {
        $this->entityCollectionSetTest('learner', 'User');
    }
}
