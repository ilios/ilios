<?php
namespace Tests\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\RelationshipVoter\AbstractVoter;
use Ilios\AuthenticationBundle\RelationshipVoter\Term as Voter;
use Ilios\AuthenticationBundle\Service\PermissionChecker;
use Ilios\CoreBundle\Entity\Term;
use Ilios\CoreBundle\Entity\School;
use Ilios\CoreBundle\Entity\Vocabulary;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class TermTest extends AbstractBase
{
    public function setup()
    {
        $this->permissionChecker = m::mock(PermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(Term::class);
    }

    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Term::class);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Term::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $vocabulary = m::mock(Vocabulary::class);
        $vocabulary->shouldReceive('getSchool')->andReturn($school);
        $entity->shouldReceive('getVocabulary')->andReturn($vocabulary);
        $this->permissionChecker->shouldReceive('canUpdateTerm')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Term::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $vocabulary = m::mock(Vocabulary::class);
        $vocabulary->shouldReceive('getSchool')->andReturn($school);
        $entity->shouldReceive('getVocabulary')->andReturn($vocabulary);
        $this->permissionChecker->shouldReceive('canUpdateTerm')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Term::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $vocabulary = m::mock(Vocabulary::class);
        $vocabulary->shouldReceive('getSchool')->andReturn($school);
        $entity->shouldReceive('getVocabulary')->andReturn($vocabulary);
        $this->permissionChecker->shouldReceive('canDeleteTerm')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Term::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $vocabulary = m::mock(Vocabulary::class);
        $vocabulary->shouldReceive('getSchool')->andReturn($school);
        $entity->shouldReceive('getVocabulary')->andReturn($vocabulary);
        $this->permissionChecker->shouldReceive('canDeleteTerm')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete delete");
    }

    public function testCanCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Term::class);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $vocabulary = m::mock(Vocabulary::class);
        $vocabulary->shouldReceive('getSchool')->andReturn($school);
        $entity->shouldReceive('getVocabulary')->andReturn($vocabulary);
        $this->permissionChecker->shouldReceive('canCreateTerm')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(Term::class);
        $school = m::mock(School::class);
        $school->shouldReceive('getId')->andReturn(1);
        $vocabulary = m::mock(Vocabulary::class);
        $vocabulary->shouldReceive('getSchool')->andReturn($school);
        $entity->shouldReceive('getVocabulary')->andReturn($vocabulary);
        $this->permissionChecker->shouldReceive('canCreateTerm')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [AbstractVoter::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }
}
