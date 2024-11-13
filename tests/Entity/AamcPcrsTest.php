<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\AamcPcrs;

/**
 * Tests for Entity AamcPcrs
 * @group model
 */
class AamcPcrsTest extends EntityBase
{
    protected AamcPcrs $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new AamcPcrs();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'id',
            'description',
        ];
        $this->validateNotBlanks($notBlank);

        $this->object->setId('test');
        $this->object->setDescription('lots of stuff');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\AamcPcrs::__construct
     */
    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getCompetencies());
    }

    /**
     * @covers \App\Entity\AamcPcrs::setDescription
     * @covers \App\Entity\AamcPcrs::getDescription
     * @covers \App\Entity\AamcPcrs::getDescription
     */
    public function testSetDescription(): void
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers \App\Entity\AamcPcrs::addCompetency
     */
    public function testAddCompetency(): void
    {
        $this->entityCollectionAddTest('competencies', 'Competency', 'getCompetencies', 'addCompetency', 'addAamcPcrs');
    }

    /**
     * @covers \App\Entity\AamcPcrs::getCompetencies
     */
    public function testGetCompetencies(): void
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
    public function testRemoveCompetency(): void
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

    protected function getObject(): AamcPcrs
    {
        return $this->object;
    }
}
