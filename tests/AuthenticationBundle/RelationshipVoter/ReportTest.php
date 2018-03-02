<?php
namespace Tests\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\RelationshipVoter\AbstractVoter;
use Ilios\AuthenticationBundle\RelationshipVoter\Report as Voter;
use Ilios\AuthenticationBundle\Service\PermissionChecker;
use Ilios\CoreBundle\Entity\Report;
use Ilios\CoreBundle\Entity\DTO\ReportDTO;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Service\Config;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ReportTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $config = m::mock(Config::class);
        $config->shouldReceive('useNewPermissionsSystem')->andReturn(true);
        $this->voter = new Voter($this->permissionChecker, $config);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(m::mock(Report::class));
    }

    public function testCanViewDTO()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $user = $token->getUser();
        $dto = m::mock(ReportDTO::class);
        $dto->shouldReceive('getUser')->andReturn(m::mock(UserInterface::class));
        $user->shouldReceive('isTheUser')->andReturn(true);
        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View DTO allowed");
    }

    public function testCanNotViewDTO()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $user = $token->getUser();
        $dto = m::mock(ReportDTO::class);
        $dto->shouldReceive('getUser')->andReturn(m::mock(UserInterface::class));
        $user->shouldReceive('isTheUser')->andReturn(false);
        $response = $this->voter->vote($token, $dto, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View DTO denied");
    }

    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $user = $token->getUser();
        $entity = m::mock(Report::class);
        $entity->shouldReceive('getUser')->andReturn(m::mock(UserInterface::class));
        $user->shouldReceive('isTheUser')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $user = $token->getUser();
        $entity = m::mock(Report::class);
        $entity->shouldReceive('getUser')->andReturn(m::mock(UserInterface::class));
        $user->shouldReceive('isTheUser')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $user = $token->getUser();
        $entity = m::mock(Report::class);
        $entity->shouldReceive('getUser')->andReturn(m::mock(UserInterface::class));
        $user->shouldReceive('isTheUser')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $user = $token->getUser();
        $entity = m::mock(Report::class);
        $entity->shouldReceive('getUser')->andReturn(m::mock(UserInterface::class));
        $user->shouldReceive('isTheUser')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $user = $token->getUser();
        $entity = m::mock(Report::class);
        $entity->shouldReceive('getUser')->andReturn(m::mock(UserInterface::class));
        $user->shouldReceive('isTheUser')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete denied");
    }

    public function testCanCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $user = $token->getUser();
        $entity = m::mock(Report::class);
        $entity->shouldReceive('getUser')->andReturn(m::mock(UserInterface::class));
        $user->shouldReceive('isTheUser')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $user = $token->getUser();
        $entity = m::mock(Report::class);
        $entity->shouldReceive('getUser')->andReturn(m::mock(UserInterface::class));
        $user->shouldReceive('isTheUser')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }
}
