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
    /**
     * @var Report
     */
    protected $object;

    /**
     * Instantiate a Report object
     */
    protected function setUp(): void
    {
        $this->object = new Report();
    }

    /**
     * @covers \App\Entity\Session::__construct
     */
    public function testConstructor()
    {
        $this->assertNotEmpty($this->object->getCreatedAt());
    }

    public function testNotBlankValidation()
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
    public function testSetSubject()
    {
        $this->basicSetTest('subject', 'string');
    }

    /**
     * @covers \App\Entity\Report::setPrepositionalObject
     * @covers \App\Entity\Report::getPrepositionalObject
     */
    public function testSetPrepositionalObject()
    {
        $this->basicSetTest('prepositionalObject', 'string');
    }

    /**
     * @covers \App\Entity\Report::setPrepositionalObjectTableRowId
     * @covers \App\Entity\Report::getPrepositionalObjectTableRowId
     */
    public function testSetPrepositionalObjectTableRowId()
    {
        $this->basicSetTest('prepositionalObjectTableRowId', 'string');
    }

    /**
     * @covers \App\Entity\Report::setTitle
     * @covers \App\Entity\Report::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\Report::setUser
     * @covers \App\Entity\Report::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }

    /**
     * @covers \App\Entity\Report::setSchool
     * @covers \App\Entity\Report::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers \App\Entity\Report::setSchool
     * @covers \App\Entity\Report::getSchool
     */
    public function testSetSchoolToNull()
    {
        $this->object->setSchool(null);
        $this->assertNull($this->object->getSchool());
    }
}
