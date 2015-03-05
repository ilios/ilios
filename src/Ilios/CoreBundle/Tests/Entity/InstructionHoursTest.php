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

    // This one seems messed up.
    // Intellisense wasn't picking up the fields and the types seem weird - maybe not properly implemented yet 

    /**
     * @covers Ilios\CoreBundle\Entity\InstructionHours::setUser
     * @covers Ilios\CoreBundle\Entity\InstructionHours::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }
}
