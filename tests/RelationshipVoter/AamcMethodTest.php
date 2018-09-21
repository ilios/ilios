<?php
namespace App\Tests\RelationshipVoter;

use App\RelationshipVoter\AbstractVoter;
use App\RelationshipVoter\AamcMethod as Voter;
use App\Service\PermissionChecker;
use App\Entity\AamcMethod;
use App\Service\Config;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class AamcMethodTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(m::mock(AamcMethod::class));
    }

    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(AamcMethod::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanNotEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(AamcMethod::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanNotDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(AamcMethod::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete denied");
    }

    public function testCanNotCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(AamcMethod::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }
}
