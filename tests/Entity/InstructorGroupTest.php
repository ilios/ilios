<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\SchoolInterface;
use App\Entity\InstructorGroup;
use Mockery as m;

/**
 * Tests for Entity InstructorGroup
 * @group model
 */
class InstructorGroupTest extends EntityBase
{
    protected InstructorGroup $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new InstructorGroup();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'title',
        ];
        $this->object->setSchool(m::mock(SchoolInterface::class));
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->validate(0);
    }

    public function testNotNullValidation(): void
    {
        $notNulls = [
            'school',
        ];
        $this->object->setTitle('test');

        $this->validateNotNulls($notNulls);
        $this->object->setSchool(m::mock(SchoolInterface::class));


        $this->validate(0);
    }

    /**
     * @covers \App\Entity\InstructorGroup::__construct
     */
    public function testConstructor(): void
    {
        $this->assertEmpty($this->object->getLearnerGroups());
        $this->assertEmpty($this->object->getIlmSessions());
        $this->assertEmpty($this->object->getOfferings());
        $this->assertEmpty($this->object->getUsers());
    }

    /**
     * @covers \App\Entity\InstructorGroup::addLearnerGroup
     */
    public function testAddLearnerGroup(): void
    {
        $this->entityCollectionAddTest('learnerGroup', 'LearnerGroup', false, false, 'addInstructorGroup');
    }

    /**
     * @covers \App\Entity\InstructorGroup::removeLearnerGroup
     */
    public function testRemoveLearnerGroup(): void
    {
        $this->entityCollectionRemoveTest('learnerGroup', 'LearnerGroup', false, false, false, 'removeInstructorGroup');
    }

    /**
     * @covers \App\Entity\InstructorGroup::getLearnerGroups
     */
    public function testGetLearnerGroups(): void
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup', false, false, 'addInstructorGroup');
    }

    /**
     * @covers \App\Entity\InstructorGroup::addIlmSession
     */
    public function testAddIlmSession(): void
    {
        $this->entityCollectionAddTest('ilmSession', 'IlmSession', false, false, 'addInstructorGroup');
    }

    /**
     * @covers \App\Entity\InstructorGroup::removeIlmSession
     */
    public function testRemoveIlmSession(): void
    {
        $this->entityCollectionRemoveTest('ilmSession', 'IlmSession', false, false, false, 'removeInstructorGroup');
    }

    /**
     * @covers \App\Entity\InstructorGroup::getIlmSessions
     */
    public function testGetIlmSessions(): void
    {
        $this->entityCollectionSetTest('ilmSession', 'IlmSession', false, false, 'addInstructorGroup');
    }

    /**
     * @covers \App\Entity\InstructorGroup::addUser
     */
    public function testAddUser(): void
    {
        $this->entityCollectionAddTest('user', 'User');
    }

    /**
     * @covers \App\Entity\InstructorGroup::removeUser
     */
    public function testRemoveUser(): void
    {
        $this->entityCollectionRemoveTest('user', 'User');
    }

    /**
     * @covers \App\Entity\InstructorGroup::getUsers
     */
    public function testGetUsers(): void
    {
        $this->entityCollectionSetTest('user', 'User');
    }

    /**
     * @covers \App\Entity\InstructorGroup::addOffering
     */
    public function testAddOffering(): void
    {
        $this->entityCollectionAddTest('offering', 'Offering');
    }

    /**
     * @covers \App\Entity\InstructorGroup::removeOffering
     */
    public function testRemoveOffering(): void
    {
        $this->entityCollectionRemoveTest('offering', 'Offering');
    }

    /**
     * @covers \App\Entity\InstructorGroup::getOfferings
     */
    public function testGetOfferings(): void
    {
        $this->entityCollectionSetTest('offering', 'Offering');
    }

    protected function getObject(): InstructorGroup
    {
        return $this->object;
    }
}
