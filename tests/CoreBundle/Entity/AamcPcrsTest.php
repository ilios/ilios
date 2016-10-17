<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\AamcPcrs;
use Mockery as m;

/**
 * Tests for Entity AamcPcrs
 */
class AamcPcrsTest extends EntityBase
{
    /**
     * @var AamcPcrs
     */
    protected $object;

    /**
     * Instantiate a AamcPcrs object
     */
    protected function setUp()
    {
        $this->object = new AamcPcrs;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'id',
            'description'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setId('test');
        $this->object->setDescription('lots of stuff');
        $this->validate(0);
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AamcPcrs::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCompetencies());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AamcPcrs::setDescription
     * @covers \Ilios\CoreBundle\Entity\AamcPcrs::getDescription
     * @covers \Ilios\CoreBundle\Entity\AamcPcrs::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AamcPcrs::addCompetency
     */
    public function testAddCompetency()
    {
        $this->entityCollectionAddTest('competencies', 'Competency', 'getCompetencies', 'addCompetency', 'addAamcPcrs');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AamcPcrs::getCompetencies
     */
    public function testGetCompetencies()
    {
        $this->entityCollectionSetTest(
            'competencies',
            'Competency',
            'getCompetencies',
            'setCompetencies',
            'addAamcPcrs'
        );
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\AamcPcrs::removeCompetency
     */
    public function testRemoveCompetency()
    {
        $this->entityCollectionRemoveTest(
            'competencies',
            'Competency',
            'getCompetencies',
            'addCompetency',
            'removeCompetency',
            'removeAamcPcrs'
        );
    }
}
