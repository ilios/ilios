<?php
namespace Tests\AuthenticationBundle\Service;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\RelationshipVoter\AbstractVoter;
use Ilios\AuthenticationBundle\RelationshipVoter\Course as Voter;
use Ilios\AuthenticationBundle\Service\PermissionChecker;
use Ilios\CoreBundle\Entity\Course;
use Ilios\CoreBundle\Entity\DTO\CourseDTO;
use Ilios\CoreBundle\Entity\School;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Mockery as m;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CourseTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /** @var  m\MockInterface */
    private $permissionChecker;

    /** @var  Voter */
    private $obj;

    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->obj = new Voter($this->permissionChecker);
    }

    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        unset($this->obj);
        unset($this->permissionChecker);
    }

    public function testAbstainsFromWrongObject()
    {
        $token = $this->createMockTokenWithSessionUser();
        $response = $this->obj->vote($token, $this, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $response);
    }

    public function testAbstainsFromWrongAttribute()
    {
        $token = $this->createMockTokenWithSessionUser();
        $response = $this->obj->vote($token, m::mock(Course::class), ['foo']);
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $response);
    }

    public function testAbstainNotViewOnDTO()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(true);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        foreach ([AbstractVoter::DELETE, AbstractVoter::CREATE, AbstractVoter::EDIT] as $attr) {
            $response = $this->obj->vote($token, m::mock(CourseDTO::class), [$attr]);
            $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $response, "${attr} abstained");
        }
    }

    public function testAllowsRootFullAccess()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(true);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        foreach ([AbstractVoter::VIEW, AbstractVoter::DELETE, AbstractVoter::CREATE, AbstractVoter::EDIT] as $attr) {
            $response = $this->obj->vote($token, m::mock(Course::class), [$attr]);
            $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "${attr} allowed");
        }
        $response = $this->obj->vote($token, m::mock(CourseDTO::class), [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    public function testCanViewDTO()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $dto = m::mock(CourseDTO::class);
        $dto->id = 1;
        $dto->school = 1;
        $this->permissionChecker->shouldReceive('canReadCourse')->andReturn(true);
        $response = $this->obj->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    public function testCanNotViewDTO()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $dto = m::mock(CourseDTO::class);
        $dto->id = 1;
        $dto->school = 1;
        $this->permissionChecker->shouldReceive('canReadCourse')->andReturn(false);
        $response = $this->obj->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "DTO View denied");
    }

    public function testCanView()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $entity = m::mock(Course::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canReadCourse')->andReturn(true);
        $response = $this->obj->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, " View allowed");
    }

    public function testCanNotView()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $entity = m::mock(Course::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canReadCourse')->andReturn(false);
        $response = $this->obj->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, " View allowed");
    }

    public function testCanEdit()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $entity = m::mock(Course::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(true);
        $response = $this->obj->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, " Edit allowed");
    }

    public function testCanNotEdit()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $entity = m::mock(Course::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canUpdateCourse')->andReturn(false);
        $response = $this->obj->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, " Edit allowed");
    }

    public function testCanDelete()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $entity = m::mock(Course::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canDeleteCourse')->andReturn(true);
        $response = $this->obj->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, " Delete allowed");
    }

    public function testCanNotDelete()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $entity = m::mock(Course::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canDeleteCourse')->andReturn(false);
        $response = $this->obj->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, " Delete allowed");
    }

    public function testCanCreate()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $entity = m::mock(Course::class);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canCreateCourse')->andReturn(true);
        $response = $this->obj->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, " Create allowed");
    }

    public function testCanNotCreate()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $entity = m::mock(Course::class);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $entity->shouldReceive('getSchool')->andReturn($school);
        $this->permissionChecker->shouldReceive('canCreateCourse')->andReturn(false);
        $response = $this->obj->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, " Create allowed");
    }

    /**
     * Creates a mock token that has the given user.
     * @param SessionUserInterface $sessionUser A (mock) user entity.
     * @return TokenInterface
     */
    protected function createMockTokenWithSessionUser(SessionUserInterface $sessionUser = null)
    {
        $mock = m::mock(TokenInterface::class);
        $mock->shouldReceive('getUser')->andReturn($sessionUser);
        return $mock;
    }
}
