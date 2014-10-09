<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\School;
use Mockery as m;

/**
 * Tests for Model School
 */
class SchoolTest extends ModelBase
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

    /**
     * @covers Ilios\CoreBundle\Model\School::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAlerts());
    }

    /**
     * @covers Ilios\CoreBundle\Model\School::getSchoolId
     */
    public function testGetSchoolId()
    {
        $this->basicGetTest('schoolId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\School::setTemplatePrefix
     */
    public function testSetTemplatePrefix()
    {
        $this->basicSetTest('templatePrefix', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\School::getTemplatePrefix
     */
    public function testGetTemplatePrefix()
    {
        $this->basicGetTest('templatePrefix', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\School::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\School::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\School::setIliosAdministratorEmail
     */
    public function testSetIliosAdministratorEmail()
    {
        $this->basicSetTest('iliosAdministratorEmail', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\School::getIliosAdministratorEmail
     */
    public function testGetIliosAdministratorEmail()
    {
        $this->basicGetTest('iliosAdministratorEmail', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\School::setDeleted
     */
    public function testSetDeleted()
    {
        $this->basicSetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\School::getDeleted
     */
    public function testGetDeleted()
    {
        $this->basicGetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\School::setChangeAlertRecipients
     */
    public function testSetChangeAlertRecipients()
    {
        $this->modelSetTest('changeAlertRecipients', 'ChangeAlertRecipients');
    }

    /**
     * @covers Ilios\CoreBundle\Model\School::getChangeAlertRecipients
     */
    public function testGetChangeAlertRecipients()
    {
        $this->modelGetTest('changeAlertRecipients', 'ChangeAlertRecipients');
    }

    /**
     * @covers Ilios\CoreBundle\Model\School::addAlert
     */
    public function testAddAlert()
    {
        $this->modelCollectionAddTest('alert', 'Alert');
    }

    /**
     * @covers Ilios\CoreBundle\Model\School::removeAlert
     */
    public function testRemoveAlert()
    {
        $this->modelCollectionRemoveTest('alert', 'Alert');
    }

    /**
     * @covers Ilios\CoreBundle\Model\School::getAlerts
     */
    public function testGetAlerts()
    {
        $this->modelCollectionGetTest('alert', 'Alert');
    }
}
