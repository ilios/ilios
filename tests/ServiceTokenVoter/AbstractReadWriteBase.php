<?php

declare(strict_types=1);

namespace App\Tests\ServiceTokenVoter;

use App\Classes\VoterPermissions;
use Mockery as m;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

abstract class AbstractReadWriteBase extends AbstractBase
{
    public function testCanRead(): void
    {
        $subject = $this->createMockSubject();

        $token = $this->createMockTokenWithServiceTokenUser();
        $this->assertEquals(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote($token, $subject, [VoterPermissions::VIEW])
        );
    }

    public function testCanNotRead(): void
    {
        $subject = $this->createMockSubject();

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

    abstract public static function writePermissionsProvider(): array;

    /**
     * @dataProvider writePermissionsProvider
     */
    public function testCanWrite(string $voterPermission): void
    {
        $subject = $this->createMockSubjectWithSchoolContext(2);

        $token = $this->createMockTokenWithServiceTokenUser([2]);
        $this->assertEquals(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote($token, $subject, [$voterPermission])
        );

        $token = $this->createMockTokenWithServiceTokenUser([1, 2]);
        $this->assertEquals(
            VoterInterface::ACCESS_GRANTED,
            $this->voter->vote($token, $subject, [$voterPermission])
        );
    }

    /**
     * @dataProvider writePermissionsProvider
     */
    public function testCanNotWrite(string $voterPermission): void
    {
        $subject = $this->createMockSubjectWithSchoolContext(2);

        $token = $this->createMockTokenWithServiceTokenUser([1]);
        $this->assertEquals(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote($token, $subject, [$voterPermission])
        );

        $subject = $this->createMockSubject();

        $token = $this->createMockTokenWithoutServiceTokenUser();
        $this->assertEquals(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote($token, $subject, [$voterPermission])
        );

        $token = $this->createMockTokenWithSessionUser();
        $this->assertEquals(
            VoterInterface::ACCESS_DENIED,
            $this->voter->vote($token, $subject, [$voterPermission])
        );
    }

    abstract protected function createMockSubject(): m\MockInterface;

    abstract protected function createMockSubjectWithSchoolContext(int $schoolId): m\MockInterface;
}
