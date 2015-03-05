<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\PublishEvent;
use Mockery as m;

/**
 * Tests for Entity PublishEvent
 */
class PublishEventTest extends EntityBase
{
    /**
     * @var PublishEvent
     */
    protected $object;

    /**
     * Instantiate a PublishEvent object
     */
    protected function setUp()
    {
        $this->object = new PublishEvent;
    }

    // This has not been set up correctly yet -- I believe this code below might have been okay 
    // if the methods had been properly defined.
    //
    // public function testNotBlankValidation()
    // {
    //     $notBlank = array(
    //         'machineIp',
    //         'timeStamp',
    //         'tableName',
    //         'tableRowId'
    //     );
    //     $this->validateNotBlanks($notBlank);

    //     $this->object->machineIp('128.128.7.6');
    //     $this->object->setTimeStamp(new \DateTime());
    //     $this->object->settableName('program');
    //     $this->object->setTableRowId(5);
    //     $this->validate(0);
    // }

    /**
     * @covers Ilios\CoreBundle\Entity\PublishEvent::setAdministrator
     * @covers Ilios\CoreBundle\Entity\PublishEvent::getAdministrator
     */
    public function testSetAdministrator()
    {
        $this->entitySetTest('administrator', 'User');
    }
}
