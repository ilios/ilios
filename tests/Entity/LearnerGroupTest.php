<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\CohortInterface;
use App\Entity\Cohort;
use App\Entity\LearnerGroup;
use App\Entity\Program;
use App\Entity\ProgramYear;
use App\Entity\School;
use App\Entity\User;
use App\Repository\LearnerGroupRepository;
use App\Validator\AllUsersInParentGroupsValidator;
use App\Validator\NoUsersInSiblingGroupsValidator;
use Mockery as m;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Validation;

/**
 * Tests for Entity LearnerGroup
 */
#[Group('model')]
#[CoversClass(LearnerGroup::class)]
final class LearnerGroupTest extends EntityBase
{
    protected LearnerGroup $object;
    protected LearnerGroupRepository|m\MockInterface $learnerGroupRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new LearnerGroup();
        $this->learnerGroupRepository = m::mock(LearnerGroupRepository::class);
        $this->learnerGroupRepository->shouldReceive('getUsersIdsInGroup')->byDefault()->andReturn([]);
        $this->learnerGroupRepository->shouldReceive('getChildUsersInGroup')->byDefault()->andReturn([]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
        unset($this->learnerGroupRepository);
    }

    /**
     * Custom validators need the repository injected; the default
     * ConstraintValidatorFactory cannot construct them.
     */
    protected function validate(int $expectedCount): array
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->setConstraintValidatorFactory(new ConstraintValidatorFactory([
                AllUsersInParentGroupsValidator::class => new AllUsersInParentGroupsValidator(
                    $this->learnerGroupRepository
                ),
                NoUsersInSiblingGroupsValidator::class => new NoUsersInSiblingGroupsValidator(
                    $this->learnerGroupRepository
                ),
            ]))
            ->getValidator();
        $errors = $validator->validate($this->getObject());
        $errorCount = count($errors);
        $parsedErrors = [];
        foreach ($errors as $error) {
            $parsedErrors[$error->getPropertyPath()] = $error->getMessage();
        }
        $this->assertEquals(
            $errorCount,
            $expectedCount,
            "Expected {$expectedCount} errors, found {$errorCount}: " .
            var_export($parsedErrors, true)
        );

        return $parsedErrors;
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'title',
        ];
        $this->object->setCohort(m::mock(CohortInterface::class));

        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('test');
        $this->object->setLocation('');
        $this->validate(0);
        $this->object->setLocation('test');
        $this->validate(0);
    }

    public function testNotNullValidation(): void
    {
        $notNulls = [
            'cohort',
        ];
        $this->object->setTitle('test');
        $this->validateNotNulls($notNulls);

        $this->object->setCohort(m::mock(CohortInterface::class));

        $this->validate(0);
    }

    public function testUsersMustBelongToParent(): void
    {
        $this->object->setTitle('test');
        $this->object->setCohort(m::mock(CohortInterface::class));

        $sharedUser = new User();
        $sharedUser->setId(1);
        $extraUser = new User();
        $extraUser->setId(2);

        $parent = new LearnerGroup();
        $parent->setId(10);

        $this->object->setParent($parent);
        $this->object->addUser($sharedUser);

        $this->learnerGroupRepository
            ->shouldReceive('getUsersIdsInGroup')
            ->with(10)
            ->andReturn([1]);
        $this->validate(0);

        $this->object->addUser($extraUser);
        $errors = $this->validate(1);
        $this->assertArrayHasKey('users', $errors);
        $this->assertSame(
            'Every user in a learner group must also be a user in its parent group.',
            $errors['users']
        );
    }

    public function testUsersValidateWithoutParent(): void
    {
        $this->object->setTitle('test');
        $this->object->setCohort(m::mock(CohortInterface::class));
        $user = new User();
        $user->setId(1);
        $this->object->addUser($user);
        $this->validate(0);
    }

    public function testUsersMayNotAppearInSiblingGroup(): void
    {
        $this->object->setTitle('test');
        $this->object->setCohort(m::mock(CohortInterface::class));

        $user1 = new User();
        $user1->setId(1);
        $user2 = new User();
        $user2->setId(2);

        $parent = new LearnerGroup();
        $parent->setId(10);

        $this->object->setParent($parent);
        $this->object->addUser($user2);

        // Sibling group 20 already has user 1; this group only has user 2.
        $this->learnerGroupRepository
            ->shouldReceive('getUsersIdsInGroup')
            ->with(10)
            ->andReturn([1, 2]);
        $this->learnerGroupRepository
            ->shouldReceive('getChildUsersInGroup')
            ->with(10)
            ->andReturn([20 => [1]]);
        $this->validate(0);

        $this->object->addUser($user1);
        $errors = $this->validate(1);
        $this->assertArrayHasKey('users', $errors);
        $this->assertSame('A user cannot be added to more than one sibling group.', $errors['users']);
    }

    public function testSiblingCheckIgnoresSelf(): void
    {
        $this->object->setTitle('test');
        $this->object->setCohort(m::mock(CohortInterface::class));
        $this->object->setId(30);

        $user = new User();
        $user->setId(1);

        $parent = new LearnerGroup();
        $parent->setId(10);

        $this->object->setParent($parent);
        $this->object->addUser($user);

        $this->learnerGroupRepository
            ->shouldReceive('getUsersIdsInGroup')
            ->with(10)
            ->andReturn([1]);
        // Repository still reports this group's own membership; the validator must ignore self.
        $this->learnerGroupRepository
            ->shouldReceive('getChildUsersInGroup')
            ->with(10)
            ->andReturn([30 => [1]]);
        $this->validate(0);
    }

    public function testConstructor(): void
    {
        $this->assertCount(0, $this->object->getIlmSessions());
        $this->assertCount(0, $this->object->getInstructorGroups());
        $this->assertCount(0, $this->object->getInstructors());
        $this->assertCount(0, $this->object->getOfferings());
        $this->assertCount(0, $this->object->getUsers());
        $this->assertCount(0, $this->object->getChildren());
        $this->assertCount(0, $this->object->getDescendants());
    }

    public function testSetTitle(): void
    {
        $this->basicSetTest('title', 'string');
    }

    public function testSetLocation(): void
    {
        $this->basicSetTest('location', 'string');
    }

    public function testSetUrl(): void
    {
        $this->basicSetTest('url', 'string');
    }

    public function testSetNeedsAccommodation(): void
    {
        $this->basicSetTest('needsAccommodation', 'bool');
    }

    public function testSetCohort(): void
    {
        $this->entitySetTest('cohort', 'Cohort');
    }

    public function testAddIlmSession(): void
    {
        $this->entityCollectionAddTest('ilmSession', 'IlmSession', false, false, 'addLearnerGroup');
    }

    public function testRemoveIlmSession(): void
    {
        $this->entityCollectionRemoveTest('ilmSession', 'IlmSession', false, false, false, 'removeLearnerGroup');
    }

    public function testGetIlmSessions(): void
    {
        $this->entityCollectionSetTest('ilmSession', 'IlmSession', false, false, 'addLearnerGroup');
    }

    public function testAddOffering(): void
    {
        $this->entityCollectionAddTest('offering', 'Offering', false, false, 'addLearnerGroup');
    }

    public function testRemoveOffering(): void
    {
        $this->entityCollectionRemoveTest('offering', 'Offering', false, false, false, 'removeLearnerGroup');
    }

    public function testGetOfferings(): void
    {
        $this->entityCollectionSetTest('offering', 'Offering', false, false, 'addLearnerGroup');
    }

    public function testAddInstructorGroup(): void
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup');
    }

    public function testRemoveInstructorGroup(): void
    {
        $this->entityCollectionRemoveTest('instructorGroup', 'InstructorGroup');
    }

    public function testGetInstructorGroups(): void
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup');
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

    public function testAddInstructor(): void
    {
        $this->entityCollectionAddTest('instructor', 'User');
    }

    public function testRemoveInstructor(): void
    {
        $this->entityCollectionRemoveTest('instructor', 'User');
    }

    public function testGetInstructors(): void
    {
        $this->entityCollectionSetTest('instructor', 'User');
    }

    public function testGetProgramYear(): void
    {
        $programYear = new ProgramYear();
        $cohort = new Cohort();
        $cohort->setProgramYear($programYear);
        $learnerGroup = new LearnerGroup();
        $learnerGroup->setCohort($cohort);
        $this->assertEquals($programYear, $learnerGroup->getProgramYear());

        $cohort = new Cohort();
        $learnerGroup = new LearnerGroup();
        $learnerGroup->setCohort($cohort);
        $this->assertNull($learnerGroup->getProgramYear());
    }

    public function testGetProgram(): void
    {
        $program = new Program();
        $programYear = new ProgramYear();
        $programYear->setProgram($program);
        $cohort = new Cohort();
        $cohort->setProgramYear($programYear);
        $learnerGroup = new LearnerGroup();
        $learnerGroup->setCohort($cohort);
        $this->assertEquals($program, $learnerGroup->getProgram());

        $cohort = new Cohort();
        $learnerGroup = new LearnerGroup();
        $learnerGroup->setCohort($cohort);
        $this->assertNull($learnerGroup->getProgram());
    }

    public function testGetSchool(): void
    {
        $school = new School();
        $program = new Program();
        $program->setSchool($school);
        $programYear = new ProgramYear();
        $programYear->setProgram($program);
        $cohort = new Cohort();
        $cohort->setProgramYear($programYear);
        $learnerGroup = new LearnerGroup();
        $learnerGroup->setCohort($cohort);
        $this->assertEquals($school, $learnerGroup->getSchool());

        $cohort = new Cohort();
        $learnerGroup = new LearnerGroup();
        $learnerGroup->setCohort($cohort);
        $this->assertNull($learnerGroup->getSchool());
    }

    public function testAddChild(): void
    {
        $this->entityCollectionAddTest('child', 'LearnerGroup', 'getChildren');
    }

    public function testRemoveChild(): void
    {
        $this->entityCollectionRemoveTest('child', 'LearnerGroup', 'getChildren');
    }

    public function testGetChildren(): void
    {
        $this->entityCollectionSetTest('child', 'LearnerGroup', 'getChildren', 'setChildren');
    }

    public function testSetAncestor(): void
    {
        $this->entitySetTest('ancestor', 'LearnerGroup');
    }

    public function testGetAncestorOrSelfWithAncestor(): void
    {
        $ancestor = m::mock(LearnerGroup::class);
        $this->object->setAncestor($ancestor);
        $this->assertSame($ancestor, $this->object->getAncestorOrSelf());
    }

    public function testGetAncestorOrSelfWithNoAncestor(): void
    {
        $this->assertSame($this->object, $this->object->getAncestorOrSelf());
    }

    public function testAddDescendant(): void
    {
        $this->entityCollectionAddTest('descendant', 'LearnerGroup', 'getDescendants', 'addDescendant', 'setAncestor');
    }

    public function testRemoveDescendant(): void
    {
        $this->entityCollectionRemoveTest('descendant', 'LearnerGroup');
    }

    public function testGetDescendants(): void
    {
        $this->entityCollectionSetTest('descendant', 'LearnerGroup', 'getDescendants', 'setDescendants', 'setAncestor');
    }

    protected function getObject(): LearnerGroup
    {
        return $this->object;
    }
}
