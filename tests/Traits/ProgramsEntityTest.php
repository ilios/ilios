<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Program;
use App\Traits\ProgramsEntity;
use Mockery as m;
use App\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversTrait;

#[CoversTrait(ProgramsEntity::class)]
final class ProgramsEntityTest extends TestCase
{
    private object $traitObject;
    public function setUp(): void
    {
        parent::setUp();
        $this->traitObject = new class {
            use ProgramsEntity;
        };
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->traitObject);
    }

    public function testSetPrograms(): void
    {
        $collection = new ArrayCollection();
        $collection->add(m::mock(Program::class));
        $collection->add(m::mock(Program::class));
        $collection->add(m::mock(Program::class));

        $this->traitObject->setPrograms($collection);
        $this->assertEquals($collection, $this->traitObject->getPrograms());
    }

    public function testRemoveProgram(): void
    {
        $collection = new ArrayCollection();
        $one = m::mock(Program::class);
        $two = m::mock(Program::class);
        $collection->add($one);
        $collection->add($two);

        $this->traitObject->setPrograms($collection);
        $this->traitObject->removeProgram($one);
        $programs = $this->traitObject->getPrograms();
        $this->assertEquals(1, $programs->count());
        $this->assertEquals($two, $programs->first());
    }

    public function testAddProgram(): void
    {
        $this->traitObject->setPrograms(new ArrayCollection());
        $this->assertCount(0, $this->traitObject->getPrograms());

        $one = m::mock(Program::class);
        $this->traitObject->addProgram($one);
        $this->assertCount(1, $this->traitObject->getPrograms());
        $this->assertEquals($one, $this->traitObject->getPrograms()->first());
        // duplicate prevention check
        $this->traitObject->addProgram($one);
        $this->assertCount(1, $this->traitObject->getPrograms());
        $this->assertEquals($one, $this->traitObject->getPrograms()->first());

        $two = m::mock(Program::class);
        $this->traitObject->addProgram($two);
        $this->assertCount(2, $this->traitObject->getPrograms());
        $this->assertEquals($one, $this->traitObject->getPrograms()->first());
        $this->assertEquals($two, $this->traitObject->getPrograms()->last());
    }
}
