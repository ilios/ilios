<?php
namespace Tests\App\Entity;

use App\Entity\AamcPcrs;
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
     * @covers \App\Entity\AamcPcrs::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCompetencies());
    }

    /**
     * @covers \App\Entity\AamcPcrs::setDescription
     * @covers \App\Entity\AamcPcrs::getDescription
     * @covers \App\Entity\AamcPcrs::getDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \App\Entity\AamcPcrs::addCompetency
     */
    public function testAddCompetency()
    {
        $this->entityCollectionAddTest('competencies', 'Competency', 'getCompetencies', 'addCompetency', 'addAamcPcrs');
    }

    /**
     * @covers \App\Entity\AamcPcrs::getCompetencies
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
     * @covers \App\Entity\AamcPcrs::removeCompetency
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
