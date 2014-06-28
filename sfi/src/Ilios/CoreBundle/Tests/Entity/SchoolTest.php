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

    /**
     * @covers Ilios\CoreBundle\Entity\School::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAlerts());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::getSchoolId
     */
    public function testGetSchoolId()
    {
        $this->basicGetTest('schoolId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::setTemplatePrefix
     */
    public function testSetTemplatePrefix()
    {
        $this->basicSetTest('templatePrefix', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::getTemplatePrefix
     */
    public function testGetTemplatePrefix()
    {
        $this->basicGetTest('templatePrefix', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::setIliosAdministratorEmail
     */
    public function testSetIliosAdministratorEmail()
    {
        $this->basicSetTest('iliosAdministratorEmail', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::getIliosAdministratorEmail
     */
    public function testGetIliosAdministratorEmail()
    {
        $this->basicGetTest('iliosAdministratorEmail', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::setDeleted
     */
    public function testSetDeleted()
    {
        $this->basicSetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::getDeleted
     */
    public function testGetDeleted()
    {
        $this->basicGetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::setChangeAlertRecipients
     */
    public function testSetChangeAlertRecipients()
    {
        $this->entitySetTest('changeAlertRecipients', 'ChangeAlertRecipients');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::getChangeAlertRecipients
     */
    public function testGetChangeAlertRecipients()
    {
        $this->entityGetTest('changeAlertRecipients', 'ChangeAlertRecipients');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::addAlert
     */
    public function testAddAlert()
    {
        $this->entityCollectionAddTest('alert', 'Alert');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::removeAlert
     */
    public function testRemoveAlert()
    {
        $this->entityCollectionRemoveTest('alert', 'Alert');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\School::getAlerts
     */
    public function testGetAlerts()
    {
        $this->entityCollectionGetTest('alert', 'Alert');
    }
}
