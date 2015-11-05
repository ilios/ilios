<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\School;
use Mockery as m;

/**
 * Tests for Entity School
 */
class SchoolTest extends EntityBase
{
    /**
     * @var School
     */
    protected $object;

    /**
     * Instantiate a School object
     */
    protected function setUp()
    {
        $this->object = new School;
    }
    
    public function testNotBlankValidation()
    {
        $notBlank = array(
            'title',
            'iliosAdministratorEmail'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->object->setIliosAdministratorEmail('dartajax@winner.net');
        $this->validate(0);
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAlerts());
        $this->assertEmpty($this->object->getStewards());
        $this->assertEmpty($this->object->getCourses());
        $this->assertEmpty($this->object->getPrograms());
        $this->assertEmpty($this->object->getTopics());
        $this->assertEmpty($this->object->getDepartments());
        $this->assertEmpty($this->object->getInstructorGroups());
        $this->assertEmpty($this->object->getCompetencies());
        $this->assertEmpty($this->object->getSessionTypes());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::setTemplatePrefix
     * @covers Ilios\CoreBundle\Entity\School::getTemplatePrefix
     */
    public function testSetTemplatePrefix()
    {
        $this->basicSetTest('templatePrefix', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::setTitle
     * @covers Ilios\CoreBundle\Entity\School::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::setIliosAdministratorEmail
     * @covers Ilios\CoreBundle\Entity\School::getIliosAdministratorEmail
     */
    public function testSetIliosAdministratorEmail()
    {
        $this->basicSetTest('iliosAdministratorEmail', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::setChangeAlertRecipients
     * @covers Ilios\CoreBundle\Entity\School::getChangeAlertRecipients
     */
    public function testSetChangeAlertRecipients()
    {
        $this->entitySetTest('changeAlertRecipients', 'ChangeAlertRecipients');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::addAlert
     */
    public function testAddAlert()
    {
        $this->entityCollectionAddTest('alert', 'Alert', false, false, 'addRecipient');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::getAlerts
     */
    public function testGetAlerts()
    {
        $this->entityCollectionSetTest('alert', 'Alert', false, false, 'addRecipient');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::addCourse
     */
    public function testAddCourse()
    {
        $this->entityCollectionAddTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::getCourses
     */
    public function testGetCourses()
    {
        $this->entityCollectionSetTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::addDepartment
     */
    public function testAddDepartment()
    {
        $this->entityCollectionAddTest('department', 'Department');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::getDepartments
     */
    public function testGetDepartments()
    {
        $this->entityCollectionSetTest('department', 'Department');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::addSteward
     */
    public function testAddSteward()
    {
        $this->entityCollectionAddTest('steward', 'ProgramYearSteward');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::getStewards
     */
    public function testGetSteward()
    {
        $this->entityCollectionSetTest('steward', 'ProgramYearSteward');
    }
}
