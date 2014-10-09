<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\AssessmentOption;
use Mockery as m;

/**
 * Tests for Model AssessmentOption
 */
class AssessmentOptionTest extends ModelBase
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
     * @covers Ilios\CoreBundle\Model\AssessmentOption::getAssessmentOptionId
     */
    public function testGetAssessmentOptionId()
    {
        $this->basicGetTest('assessmentOptionId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AssessmentOption::setName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AssessmentOption::getName
     */
    public function testGetName()
    {
        $this->basicGetTest('name', 'string');
    }
}
