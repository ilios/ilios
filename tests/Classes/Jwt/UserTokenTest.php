<?php

declare(strict_types=1);

namespace App\Tests\Classes\Jwt;

use App\Classes\Jwt\UserToken;
use App\Tests\TestCase;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(UserToken::class)]
final class UserTokenTest extends TestCase
{
    #[DataProvider('dataProvider')]
    public function testConstructor(
        DateTimeImmutable $iat,
        DateTimeImmutable $exp,
        array $aud,
        string $iss,
        int $userId,
        bool $isRoot,
        bool $performsNonLearnerFunction,
        bool $canCreateOrUpdateUserInAnySchool,
        ?int $issuedWith,
        DateTimeImmutable $firstCreatedAt,
        int $refreshCount,
    ): void {
        $token = new UserToken(
            $iat,
            $exp,
            $aud,
            $iss,
            $userId,
            $isRoot,
            $performsNonLearnerFunction,
            $canCreateOrUpdateUserInAnySchool,
            $issuedWith,
            $firstCreatedAt,
            $refreshCount,
        );
        $this->assertSame($iat->format('U'), $token->issuedAt->format('U'));
        $this->assertSame($exp->format('U'), $token->expiresAt->format('U'));
        $this->assertSame($aud, $token->audience);
        $this->assertSame($iss, $token->issuer);
        $this->assertSame($isRoot, $token->isRoot);
        $this->assertSame($performsNonLearnerFunction, $token->performsNonLearnerFunction);
        $this->assertSame($canCreateOrUpdateUserInAnySchool, $token->canCreateOrUpdateUserInAnySchool);
        $this->assertSame($issuedWith, $token->issuedWith);
        $this->assertSame($firstCreatedAt, $token->firstCreatedAt);
        $this->assertSame($refreshCount, $token->refreshCount);
    }

    #[DataProvider('dataProvider')]
    public function testToArray(
        DateTimeImmutable $iat,
        DateTimeImmutable $exp,
        array $aud,
        string $iss,
        int $userId,
        bool $isRoot,
        bool $performsNonLearnerFunction,
        bool $canCreateOrUpdateUserInAnySchool,
        ?int $issuedWith,
        DateTimeImmutable $firstCreatedAt,
        int $refreshCount,
    ): void {
        $token = new UserToken(
            $iat,
            $exp,
            $aud,
            $iss,
            $userId,
            $isRoot,
            $performsNonLearnerFunction,
            $canCreateOrUpdateUserInAnySchool,
            $issuedWith,
            $firstCreatedAt,
            $refreshCount,
        );
        $arr = $token->toArray();
        $issuedWithIncluded = !is_null($issuedWith);
        $this->assertCount($issuedWithIncluded ? 11 : 10, $arr);
        $this->assertSame($token->issuedAt->format('U'), $arr['iat']);
        $this->assertSame($token->expiresAt->format('U'), $arr['exp']);
        $this->assertSame($token->audience, $arr['aud']);
        $this->assertSame($token->issuer, $arr['iss']);
        $this->assertSame($token->userId, $arr['user_id']);
        $this->assertSame($token->isRoot, $arr['is_root']);
        $this->assertSame($token->performsNonLearnerFunction, $arr['performs_non_learner_function']);
        $this->assertSame($token->canCreateOrUpdateUserInAnySchool, $arr['can_create_or_update_user_in_any_school']);
        $this->assertSame($token->firstCreatedAt->format('U'), $arr['firstCreatedAt']);
        $this->assertSame($token->refreshCount, $arr['refreshCount']);
        if ($issuedWithIncluded) {
            $this->assertSame($token->issuedWith, $arr['issued_with']);
        }
    }

    public function testToArrayWithSingleItemAudienceArray(): void
    {
        $aud = 'just_this';
        $token = new UserToken(
            new DateTimeImmutable(),
            new DateTimeImmutable()->add(new DateInterval('PT5M')),
            [$aud],
            'foo',
            1,
            false,
            false,
            false,
            null,
            new DateTimeImmutable(),
            0,
        );
        $arr = $token->toArray();
        $this->assertSame($aud, $arr['aud']);
    }

    public static function dataProvider(): array
    {
        return [
            [
                new DateTimeImmutable(),
                new DateTimeImmutable(),
                ['foo', 'bar'],
                'bar',
                123,
                true,
                false,
                true,
                123,
                new DateTimeImmutable(),
                10,
            ],
            [
                new DateTimeImmutable(),
                new DateTimeImmutable(),
                [],
                'baz',
                234,
                false,
                true,
                false,
                null,
                new DateTimeImmutable(),
                30,
            ],
        ];
    }
}
