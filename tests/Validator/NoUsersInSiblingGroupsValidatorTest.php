<?php

declare(strict_types=1);

namespace App\Tests\Validator;

use App\Entity\LearnerGroup;
use App\Entity\UserInterface;
use App\Repository\LearnerGroupRepository;
use App\Validator\AllUsersInParentGroups;
use App\Validator\NoUsersInSiblingGroups;
use App\Validator\NoUsersInSiblingGroupsValidator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

#[CoversClass(NoUsersInSiblingGroupsValidator::class)]
final class NoUsersInSiblingGroupsValidatorTest extends ConstraintValidatorTestCase
{
    use MockeryPHPUnitIntegration;

    private m\MockInterface|LearnerGroupRepository $repository;

    protected function setUp(): void
    {
        $this->repository = m::mock(LearnerGroupRepository::class);
        parent::setUp();
    }

    protected function tearDown(): void
    {
        unset($this->repository);
        parent::tearDown();
    }

    protected function createValidator(): ConstraintValidatorInterface
    {
        return new NoUsersInSiblingGroupsValidator($this->repository);
    }

    public function testRejectsTheWrongConstraintType(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validate(new LearnerGroup(), new AllUsersInParentGroups());
    }

    public function testRejectsNonLearnerGroupValues(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $this->validate('not-a-learner-group', new NoUsersInSiblingGroups());
    }

    public function testTopLevelGroupsWithoutAParentAreValid(): void
    {
        $this->repository->shouldNotReceive('getChildUsersInGroup');

        $group = new LearnerGroup();

        $this->validate($group, new NoUsersInSiblingGroups());

        $this->assertNoViolation();
    }

    public function testAGroupsOwnUsersAreNotTreatedAsSiblingUsers(): void
    {
        // The repository returns the group's own entry; after the validator
        // removes itself from the sibling map there are no siblings left.
        $group = $this->buildGroupWithParent(parentId: 1, groupId: 5);
        $group->addUser($this->getMockUser(3));

        $this->repository
            ->shouldReceive('getChildUsersInGroup')
            ->once()
            ->with(1)
            ->andReturn([5 => [3]]);

        $this->validate($group, new NoUsersInSiblingGroups());

        $this->assertNoViolation();
    }

    public function testGroupsWhoseUsersAreNotInAnySiblingAreValid(): void
    {
        $group = $this->buildGroupWithParent(parentId: 1, groupId: 5);
        $group->addUser($this->getMockUser(3));

        $this->repository
            ->shouldReceive('getChildUsersInGroup')
            ->once()
            ->with(1)
            ->andReturn([6 => [7, 8]]);

        $this->validate($group, new NoUsersInSiblingGroups());

        $this->assertNoViolation();
    }

    public function testUsersSharedWithASiblingAreInvalid(): void
    {
        $group = $this->buildGroupWithParent(parentId: 1, groupId: 5);
        $group->addUser($this->getMockUser(7));

        $this->repository
            ->shouldReceive('getChildUsersInGroup')
            ->once()
            ->with(1)
            ->andReturn([5 => [1], 6 => [7, 8]]);

        $constraint = new NoUsersInSiblingGroups();
        $this->validate($group, $constraint);

        $this->buildViolation($constraint->message)
            ->atPath('property.path.users')
            ->assertRaised();
    }

    public function testUnpersistedGroupsDoNotAccessTheirUninitializedId(): void
    {
        $parent = new LearnerGroup();
        $parent->setId(1);

        $group = new LearnerGroup();
        $group->setParent($parent);
        $group->addUser($this->getMockUser(7));

        $this->repository
            ->shouldReceive('getChildUsersInGroup')
            ->once()
            ->with(1)
            ->andReturn([6 => [7, 8]]);

        $constraint = new NoUsersInSiblingGroups();
        $this->validate($group, $constraint);

        $this->buildViolation($constraint->message)
            ->atPath('property.path.users')
            ->assertRaised();
    }

    public function testValidationStopsAtTheFirstUserSharedWithASibling(): void
    {
        $group = $this->buildGroupWithParent(parentId: 1, groupId: 5);
        $group->addUser($this->getMockUser(7));
        $group->addUser($this->getMockUser(8));

        $this->repository
            ->shouldReceive('getChildUsersInGroup')
            ->once()
            ->with(1)
            ->andReturn([6 => [7, 8]]);

        $constraint = new NoUsersInSiblingGroups();
        $this->validate($group, $constraint);

        $this->buildViolation($constraint->message)
            ->atPath('property.path.users')
            ->assertRaised();
    }

    private function buildGroupWithParent(int $parentId, int $groupId): LearnerGroup
    {
        $parent = new LearnerGroup();
        $parent->setId($parentId);

        $group = new LearnerGroup();
        $group->setParent($parent);
        $group->setId($groupId);

        return $group;
    }

    private function getMockUser(int $id): UserInterface
    {
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn($id);

        return $user;
    }
}
