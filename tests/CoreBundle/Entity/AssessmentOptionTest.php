<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\AssessmentOption;
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
     * @covers \Ilios\CoreBundle\Entity\AssessmentOption::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getSessionTypes());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AssessmentOption::setName
     * @covers \Ilios\CoreBundle\Entity\AssessmentOption::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AssessmentOption::addSessionType
     */
    public function testAddSessionType()
    {
        $this->entityCollectionAddTest('sessionType', 'SessionType');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AssessmentOption::removeSessionType
     */
    public function testRemoveSessionType()
    {
        $this->entityCollectionRemoveTest('sessionType', 'SessionType');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AssessmentOption::getSessionTypes
     */
    public function testGetSessionTypes()
    {
        $this->entityCollectionSetTest('sessionType', 'SessionType');
    }
}
