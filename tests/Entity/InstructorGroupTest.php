<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\SchoolInterface;
use App\Entity\InstructorGroup;
use Mockery as m;

/**
 * Tests for Entity InstructorGroup
 */
#[\PHPUnit\Framework\Attributes\Group('model')]
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Entity\InstructorGroup::class)]
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

    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getLearnerGroups());
        $this->assertCount(0, $this->object->getIlmSessions());
        $this->assertCount(0, $this->object->getOfferings());
        $this->assertCount(0, $this->object->getUsers());
    }

    public function testAddLearnerGroup(): void
    {
        $this->entityCollectionAddTest('learnerGroup', 'LearnerGroup', false, false, 'addInstructorGroup');
    }

    public function testRemoveLearnerGroup(): void
    {
        $this->entityCollectionRemoveTest('learnerGroup', 'LearnerGroup', false, false, false, 'removeInstructorGroup');
    }

    public function testGetLearnerGroups(): void
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup', false, false, 'addInstructorGroup');
    }

    public function testAddIlmSession(): void
    {
        $this->entityCollectionAddTest('ilmSession', 'IlmSession', false, false, 'addInstructorGroup');
    }

    public function testRemoveIlmSession(): void
    {
        $this->entityCollectionRemoveTest('ilmSession', 'IlmSession', false, false, false, 'removeInstructorGroup');
    }

    public function testGetIlmSessions(): void
    {
        $this->entityCollectionSetTest('ilmSession', 'IlmSession', false, false, 'addInstructorGroup');
    }

    public function testAddUser(): void
    {
        $this->entityCollectionAddTest('user', 'User');
    }

    public function testRemoveUser(): void
    {
        $this->entityCollectionRemoveTest('user', 'User');
    }

    public function testGetUsers(): void
    {
        $this->entityCollectionSetTest('user', 'User');
    }

    public function testAddOffering(): void
    {
        $this->entityCollectionAddTest('offering', 'Offering');
    }

    public function testRemoveOffering(): void
    {
        $this->entityCollectionRemoveTest('offering', 'Offering');
    }

    public function testGetOfferings(): void
    {
        $this->entityCollectionSetTest('offering', 'Offering');
    }

    protected function getObject(): InstructorGroup
    {
        return $this->object;
    }
}
