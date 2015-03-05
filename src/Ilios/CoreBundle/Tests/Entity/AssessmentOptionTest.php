<?php
namespace Ilios\CoreBundle\Tests\Entity;

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
     * @covers Ilios\CoreBundle\Entity\AssessmentOption::setName
     * @covers Ilios\CoreBundle\Entity\AssessmentOption::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }
}
