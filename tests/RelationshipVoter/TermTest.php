<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Classes\VoterPermissions;
use App\Entity\SchoolInterface;
use App\Entity\TermInterface;
use App\Entity\VocabularyInterface;
use App\RelationshipVoter\Term as Voter;
use App\Service\SessionUserPermissionChecker;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class TermTest extends AbstractBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->permissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->voter = new Voter($this->permissionChecker);
    }

    public function testAllowsRootFullAccess()
    {
        $this->checkRootEntityAccess(m::mock(TermInterface::class));
    }

    public function testCanView()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(TermInterface::class);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "View allowed");
    }

    public function testCanEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(TermInterface::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $vocabulary = m::mock(VocabularyInterface::class);
        $vocabulary->shouldReceive('getSchool')->andReturn($school);
        $entity->shouldReceive('getVocabulary')->andReturn($vocabulary);
        $this->permissionChecker->shouldReceive('canUpdateTerm')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Edit allowed");
    }

    public function testCanNotEdit()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(TermInterface::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $vocabulary = m::mock(VocabularyInterface::class);
        $vocabulary->shouldReceive('getSchool')->andReturn($school);
        $entity->shouldReceive('getVocabulary')->andReturn($vocabulary);
        $this->permissionChecker->shouldReceive('canUpdateTerm')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::EDIT]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Edit denied");
    }

    public function testCanDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(TermInterface::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $vocabulary = m::mock(VocabularyInterface::class);
        $vocabulary->shouldReceive('getSchool')->andReturn($school);
        $entity->shouldReceive('getVocabulary')->andReturn($vocabulary);
        $this->permissionChecker->shouldReceive('canDeleteTerm')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Delete allowed");
    }

    public function testCanNotDelete()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(TermInterface::class);
        $entity->shouldReceive('getId')->andReturn(1);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $vocabulary = m::mock(VocabularyInterface::class);
        $vocabulary->shouldReceive('getSchool')->andReturn($school);
        $entity->shouldReceive('getVocabulary')->andReturn($vocabulary);
        $this->permissionChecker->shouldReceive('canDeleteTerm')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::DELETE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Delete delete");
    }

    public function testCanCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(TermInterface::class);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $vocabulary = m::mock(VocabularyInterface::class);
        $vocabulary->shouldReceive('getSchool')->andReturn($school);
        $entity->shouldReceive('getVocabulary')->andReturn($vocabulary);
        $this->permissionChecker->shouldReceive('canCreateTerm')->andReturn(true);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "Create allowed");
    }

    public function testCanNotCreate()
    {
        $token = $this->createMockTokenWithNonRootSessionUser();
        $entity = m::mock(TermInterface::class);
        $school = m::mock(SchoolInterface::class);
        $school->shouldReceive('getId')->andReturn(1);
        $vocabulary = m::mock(VocabularyInterface::class);
        $vocabulary->shouldReceive('getSchool')->andReturn($school);
        $entity->shouldReceive('getVocabulary')->andReturn($vocabulary);
        $this->permissionChecker->shouldReceive('canCreateTerm')->andReturn(false);
        $response = $this->voter->vote($token, $entity, [VoterPermissions::CREATE]);
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $response, "Create denied");
    }

    public function supportsTypeProvider(): array
    {
        return [
            [TermInterface::class, true],
            [self::class, false],
        ];
    }

    public function supportsAttributesProvider(): array
    {
        return [
            [VoterPermissions::VIEW, true],
            [VoterPermissions::CREATE, true],
            [VoterPermissions::DELETE, true],
            [VoterPermissions::EDIT, true],
            [VoterPermissions::LOCK, false],
            [VoterPermissions::UNLOCK, false],
            [VoterPermissions::ROLLOVER, false],
            [VoterPermissions::CREATE_TEMPORARY_FILE, false],
            [VoterPermissions::VIEW_DRAFT_CONTENTS, false],
            [VoterPermissions::VIEW_VIRTUAL_LINK, false],
            [VoterPermissions::ARCHIVE, false],
        ];
    }
}
