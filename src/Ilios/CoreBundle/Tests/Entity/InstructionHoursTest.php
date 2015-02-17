<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\InstructionHours;
use Mockery as m;

/**
 * Tests for Entity InstructionHours
 */
class InstructionHoursTest extends EntityBase
{
    /**
     * @var InstructionHours
     */
    protected $object;

    /**
     * Instantiate a InstructionHours object
     */
    protected function setUp()
    {
        $this->object = new InstructionHours;
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructionHours::setUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }
}
