<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\LearnerGroup;
use Mockery as m;

/**
 * Tests for Entity LearnerGroup
 */
class LearnerGroupTest extends EntityBase
{
    /**
     * @var LearnerGroup
     */
    protected $object;

    /**
     * Instantiate a LearnerGroup object
     */
    protected function setUp()
    {
        $this->object = new LearnerGroup;
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
     * @covers Ilios\CoreBundle\Entity\LearnerGroup::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getIlmSessions());
        $this->assertEmpty($this->object->getInstructorGroups());
        $this->assertEmpty($this->object->getInstructorUsers());
        $this->assertEmpty($this->object->getOfferings());
        $this->assertEmpty($this->object->getUsers());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearnerGroup::setTitle
     * @covers Ilios\CoreBundle\Entity\LearnerGroup::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearnerGroup::setInstructors
     * @covers Ilios\CoreBundle\Entity\LearnerGroup::getInstructors
     */
    public function testSetInstructors()
    {
        $this->basicSetTest('instructors', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearnerGroup::setLocation
     * @covers Ilios\CoreBundle\Entity\LearnerGroup::getLocation
     */
    public function testSetLocation()
    {
        $this->basicSetTest('location', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearnerGroup::setCohort
     * @covers Ilios\CoreBundle\Entity\LearnerGroup::getCohort
     */
    public function testSetCohort()
    {
        $this->entitySetTest('cohort', 'Cohort');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\LearnerGroup::addUser
     */
    public function testAddUser()
    {
        $this->entityCollectionAddTest('user', 'User');
    }
}
