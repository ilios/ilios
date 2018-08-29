<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\AssessmentOption;
use Mockery as m;

/**
 * Tests for Entity AssessmentOption
 */
class AssessmentOptionTest extends EntityBase
{
    /**
     * @var AssessmentOption
     */
    protected $object;

    /**
     * Instantiate a AssessmentOption object
     */
    protected function setUp()
    {
        $this->object = new AssessmentOption;
    }


    public function testNotBlankValidation()
    {
        $notBlank = array(
            'name'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setName('Smorgasbord');
        $this->validate(0);
    }

    /**
     * @covers \AppBundle\Entity\AssessmentOption::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getSessionTypes());
    }

    /**
     * @covers \AppBundle\Entity\AssessmentOption::setName
     * @covers \AppBundle\Entity\AssessmentOption::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \AppBundle\Entity\AssessmentOption::addSessionType
     */
    public function testAddSessionType()
    {
        $this->entityCollectionAddTest('sessionType', 'SessionType');
    }

    /**
     * @covers \AppBundle\Entity\AssessmentOption::removeSessionType
     */
    public function testRemoveSessionType()
    {
        $this->entityCollectionRemoveTest('sessionType', 'SessionType');
    }

    /**
     * @covers \AppBundle\Entity\AssessmentOption::getSessionTypes
     */
    public function testGetSessionTypes()
    {
        $this->entityCollectionSetTest('sessionType', 'SessionType');
    }
}
