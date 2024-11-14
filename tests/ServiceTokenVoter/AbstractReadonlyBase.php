<?php

declare(strict_types=1);

namespace App\Tests\ServiceTokenVoter;

use App\Classes\VoterPermissions;
use App\ServiceTokenVoter\ReadonlyEntityVoter as Voter;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

abstract class AbstractReadonlyBase extends AbstractBase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->voter = new Voter();
    }

    public static function supportsAttributesProvider(): array
    {
        return [
            [VoterPermissions::VIEW, true],
            [VoterPermissions::CREATE, false],
            [VoterPermissions::DELETE, false],
            [VoterPermissions::EDIT, false],
            [VoterPermissions::LOCK, false],
            [VoterPermissions::UNLOCK, false],
            [VoterPermissions::ROLLOVER, false],
            [VoterPermissions::CREATE_TEMPORARY_FILE, false],
            [VoterPermissions::VIEW_DRAFT_CONTENTS, false],
            [VoterPermissions::VIEW_VIRTUAL_LINK, false],
            [VoterPermissions::ARCHIVE, false],
        ];
    }

    abstract public static function subjectProvider(): array;

    #[\PHPUnit\Framework\Attributes\DataProvider('subjectProvider')]
    public function canReadTest(string $className): void
    {
        $subject = m::mock($className);
        $token = $this->createMockTokenWithServiceTokenUser();
        $this->assertEquals(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote($token, $subject, [VoterPermissions::VIEW])
        );
    }

    public function canNotReadTest(string $className): void
    {
        $subject = m::mock($className);

        $token = $this->createMockTokenWithoutServiceTokenUser();
        $this->assertEquals(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote($token, $subject, [VoterPermissions::VIEW])
        );

        $token = $this->createMockTokenWithSessionUser();
        $this->assertEquals(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote($token, $subject, [VoterPermissions::VIEW])
        );
    }
}
