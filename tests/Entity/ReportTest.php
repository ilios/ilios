<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Report;
use App\Entity\UserInterface;
use Mockery as m;

/**
 * Tests for Entity Report
 * @group model
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\Report::class)]
class ReportTest extends EntityBase
{
    protected Report $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new Report();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotNullValidation(): void
    {
        $this->object->setSubject('test');
        $this->validateNotNulls(['user']);
        $this->object->setUser(m::mock(UserInterface::class));
        $this->object->setTitle('');
        $this->object->setPrepositionalObject('');
        $this->object->setPrepositionalObjectTableRowId('');
        $this->validate(0);
        $this->object->setTitle('test');
        $this->object->setPrepositionalObject('test');
        $this->object->setPrepositionalObjectTableRowId('test');
        $this->validate(0);
    }

    public function testNotBlankValidation(): void
    {
        $this->object->setUser(m::mock(UserInterface::class));
        $this->validateNotBlanks(['subject']);
        $this->object->setSubject('test');
        $this->validate(0);
    }

    public function testSetSubject(): void
    {
        $this->basicSetTest('subject', 'string');
    }

    public function testSetPrepositionalObject(): void
    {
        $this->basicSetTest('prepositionalObject', 'string');
    }

    public function testSetPrepositionalObjectTableRowId(): void
    {
        $this->basicSetTest('prepositionalObjectTableRowId', 'string');
    }

    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    public function testSetUser(): void
    {
        $this->entitySetTest('user', 'User');
    }

    public function testSetSchool(): void
    {
        $this->entitySetTest('school', 'School');
    }

    public function testSetSchoolToNull(): void
    {
        $this->object->setSchool(null);
        $this->assertNull($this->object->getSchool());
    }

    protected function getObject(): Report
    {
        return $this->object;
    }
}
