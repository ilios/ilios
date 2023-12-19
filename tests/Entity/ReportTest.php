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
class ReportTest extends EntityBase
{
    protected function setUp(): void
    {
        $this->object = new Report();
    }

    /**
     * @covers \App\Entity\Session::__construct
     */
    public function testConstructor(): void
    {
        $this->assertNotEmpty($this->object->getCreatedAt());
    }

    public function testNotBlankValidation(): void
    {
        $errors = $this->validate(2);
        $this->assertEquals([
            "subject" => "NotBlank",
            "user" => "NotNull",
        ], $errors);

        $this->object->setUser(m::mock(UserInterface::class));
        $this->object->setSubject('test');
        $this->object->setTitle('');
        $this->object->setPrepositionalObject('');
        $this->object->setPrepositionalObjectTableRowId('');
        $this->validate(0);
        $this->object->setTitle('test');
        $this->object->setPrepositionalObject('test');
        $this->object->setPrepositionalObjectTableRowId('test');
        $this->validate(0);
    }

    /**
     * @covers \App\Entity\Report::setSubject
     * @covers \App\Entity\Report::getSubject
     */
    public function testSetSubject(): void
    {
        $this->basicSetTest('subject', 'string');
    }

    /**
     * @covers \App\Entity\Report::setPrepositionalObject
     * @covers \App\Entity\Report::getPrepositionalObject
     */
    public function testSetPrepositionalObject(): void
    {
        $this->basicSetTest('prepositionalObject', 'string');
    }

    /**
     * @covers \App\Entity\Report::setPrepositionalObjectTableRowId
     * @covers \App\Entity\Report::getPrepositionalObjectTableRowId
     */
    public function testSetPrepositionalObjectTableRowId(): void
    {
        $this->basicSetTest('prepositionalObjectTableRowId', 'string');
    }

    /**
     * @covers \App\Entity\Report::setTitle
     * @covers \App\Entity\Report::getTitle
     */
    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\Report::setUser
     * @covers \App\Entity\Report::getUser
     */
    public function testSetUser(): void
    {
        $this->entitySetTest('user', 'User');
    }

    /**
     * @covers \App\Entity\Report::setSchool
     * @covers \App\Entity\Report::getSchool
     */
    public function testSetSchool(): void
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \App\Entity\Report::setSchool
     * @covers \App\Entity\Report::getSchool
     */
    public function testSetSchoolToNull(): void
    {
        $this->object->setSchool(null);
        $this->assertNull($this->object->getSchool());
    }
}
