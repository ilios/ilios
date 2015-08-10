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
     * @covers Ilios\CoreBundle\Entity\School::setDeleted
     * @covers Ilios\CoreBundle\Entity\School::isDeleted
     */
    public function testSetDeleted()
    {
        $this->booleanSetTest('deleted');
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
        $this->entityCollectionAddTest('alert', 'Alert');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::getAlerts
     */
    public function testGetAlerts()
    {
        $this->entityCollectionSetTest('alert', 'Alert');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::addCourse
     */
    public function testAddCourse()
    {
        $this->softDeleteEntityCollectionAddTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::getCourses
     */
    public function testGetCourses()
    {
        $this->softDeleteEntityCollectionSetTest('course', 'Course');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::addDepartment
     */
    public function testAddDepartment()
    {
        $this->softDeleteEntityCollectionAddTest('department', 'Department');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::getDepartments
     */
    public function testGetDepartments()
    {
        $this->softDeleteEntityCollectionSetTest('department', 'Department');
    }
}
