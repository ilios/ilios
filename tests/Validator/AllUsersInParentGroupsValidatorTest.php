<?php

declare(strict_types=1);

namespace App\Tests\Validator;

use App\Entity\LearnerGroup;
use App\Entity\UserInterface;
use App\Repository\LearnerGroupRepository;
use App\Validator\AllUsersInParentGroups;
use App\Validator\AllUsersInParentGroupsValidator;
use App\Validator\NoUsersInSiblingGroups;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

#[CoversClass(AllUsersInParentGroupsValidator::class)]
final class AllUsersInParentGroupsValidatorTest extends ConstraintValidatorTestCase
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
        parent::tearDown();
        unset($this->repository);
    }

    protected function createValidator(): ConstraintValidatorInterface
    {
        return new AllUsersInParentGroupsValidator($this->repository);
    }

    public function testRejectsTheWrongConstraintType(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->validate(new LearnerGroup(), new NoUsersInSiblingGroups());
    }

    public function testRejectsNonLearnerGroupValues(): void
    {
        $this->expectException(UnexpectedValueException::class);

        $this->validate('not-a-learner-group', new AllUsersInParentGroups());
    }

    public function testTopLevelGroupsWithoutAParentAreValid(): void
    {
        $this->repository->shouldNotReceive('getUsersIdsInGroup');

        $group = new LearnerGroup();

        $this->validate($group, new AllUsersInParentGroups());

        $this->assertNoViolation();
    }

    public function testGroupsWithNoUsersAreValid(): void
    {
        $parent = new LearnerGroup();
        $parent->setId(1);

        $group = new LearnerGroup();
        $group->setParent($parent);

        $this->repository
            ->shouldReceive('getUsersIdsInGroup')
            ->once()
            ->with(1)
            ->andReturn([2, 3]);

        $this->validate($group, new AllUsersInParentGroups());

        $this->assertNoViolation();
    }

    public function testGroupsWhoseUsersAreAllInTheParentAreValid(): void
    {
        $parent = new LearnerGroup();
        $parent->setId(1);

        $group = new LearnerGroup();
        $group->setParent($parent);
        $group->addUser($this->getMockUser(2));
        $group->addUser($this->getMockUser(3));

        $this->repository
            ->shouldReceive('getUsersIdsInGroup')
            ->once()
            ->with(1)
            ->andReturn([2, 3, 4]);

        $this->validate($group, new AllUsersInParentGroups());

        $this->assertNoViolation();
    }

    public function testUsersMissingFromTheParentAreInvalid(): void
    {
        $parent = new LearnerGroup();
        $parent->setId(1);

        $group = new LearnerGroup();
        $group->setParent($parent);
        $group->addUser($this->getMockUser(2));
        $group->addUser($this->getMockUser(99));

        $this->repository
            ->shouldReceive('getUsersIdsInGroup')
            ->once()
            ->with(1)
            ->andReturn([2, 3]);

        $constraint = new AllUsersInParentGroups();
        $this->validate($group, $constraint);

        $this->buildViolation($constraint->message)
            ->atPath('property.path.users')
            ->assertRaised();
    }

    public function testGroupsAreInvalidWhenTheParentHasNoUsers(): void
    {
        $parent = new LearnerGroup();
        $parent->setId(1);

        $group = new LearnerGroup();
        $group->setParent($parent);
        $group->addUser($this->getMockUser(2));

        $this->repository
            ->shouldReceive('getUsersIdsInGroup')
            ->once()
            ->with(1)
            ->andReturn([]);

        $constraint = new AllUsersInParentGroups();
        $this->validate($group, $constraint);

        $this->buildViolation($constraint->message)
            ->atPath('property.path.users')
            ->assertRaised();
    }

    public function testValidationStopsAtTheFirstUserMissingFromTheParent(): void
    {
        $parent = new LearnerGroup();
        $parent->setId(1);

        $group = new LearnerGroup();
        $group->setParent($parent);
        $group->addUser($this->getMockUser(50));
        $group->addUser($this->getMockUser(51));

        $this->repository
            ->shouldReceive('getUsersIdsInGroup')
            ->once()
            ->with(1)
            ->andReturn([1]);

        $constraint = new AllUsersInParentGroups();
        $this->validate($group, $constraint);

        $this->buildViolation($constraint->message)
            ->atPath('property.path.users')
            ->assertRaised();
    }

    private function getMockUser(int $id): UserInterface
    {
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getId')->andReturn($id);

        return $user;
    }
}
