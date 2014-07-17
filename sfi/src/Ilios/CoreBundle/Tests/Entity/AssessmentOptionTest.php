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
    

    /**
     * @covers Ilios\CoreBundle\Entity\AssessmentOption::getAssessmentOptionId
     */
    public function testGetAssessmentOptionId()
    {
        $this->basicGetTest('assessmentOptionId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\AssessmentOption::setName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\AssessmentOption::getName
     */
    public function testGetName()
    {
        $this->basicGetTest('name', 'string');
    }
}
