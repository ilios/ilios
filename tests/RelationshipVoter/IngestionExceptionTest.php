<?php
namespace App\Tests\RelationshipVoter;

use App\RelationshipVoter\AbstractVoter;
use App\RelationshipVoter\IngestionException as Voter;
use App\Service\PermissionChecker;
use App\Entity\IngestionException;
use App\Service\Config;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class IngestionExceptionTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(m::mock(IngestionException::class), [AbstractVoter::VIEW]);
    }


    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(IngestionException::class);
        $token->getUser()->shouldReceive('performsNonLearnerFunction')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanNotView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(IngestionException::class);
        $token->getUser()->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "View denied");
    }
}
