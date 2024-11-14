<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\AamcPcrs;

/**
 * Tests for Entity AamcPcrs
 * @group model
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\AamcPcrs::class)]
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

    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getCompetencies());
    }

    public function testSetDescription(): void
    {
        $this->basicSetTest('description', 'string');
    }

    public function testAddCompetency(): void
    {
        $this->entityCollectionAddTest('competencies', 'Competency', 'getCompetencies', 'addCompetency', 'addAamcPcrs');
    }

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
