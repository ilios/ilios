<?php

declare(strict_types=1);

namespace App\Tests\Classes;

use App\Classes\UserToken;
use App\Tests\TestCase;
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
        ?int $issuedWith,
        DateTimeImmutable $firstCreatedAt,
        int $refreshCount,
        int $refreshLimit
    ): void {
        $token = new UserToken(
            $iat,
            $exp,
            $aud,
            $iss,
            $userId,
            $isRoot,
            $performsNonLearnerFunction,
            $issuedWith,
            $firstCreatedAt,
            $refreshCount,
            $refreshLimit
        );
        $this->assertSame($iat->format('U'), $token->issuedAt->format('U'));
        $this->assertSame($exp->format('U'), $token->expiresAt->format('U'));
        $this->assertSame($aud, $token->audience);
        $this->assertSame($iss, $token->issuer);
        $this->assertSame($isRoot, $token->isRoot);
        $this->assertSame($performsNonLearnerFunction, $token->performsNonLearnerFunction);
        $this->assertSame($issuedWith, $token->issuedWith);
        $this->assertSame($firstCreatedAt, $token->firstCreatedAt);
        $this->assertSame($refreshCount, $token->refreshCount);
        $this->assertSame($refreshLimit, $token->refreshLimit);
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
        ?int $issuedWith,
        DateTimeImmutable $firstCreatedAt,
        int $refreshCount,
        int $refreshLimit
    ): void {
        $token = new UserToken(
            $iat,
            $exp,
            $aud,
            $iss,
            $userId,
            $isRoot,
            $performsNonLearnerFunction,
            $issuedWith,
            $firstCreatedAt,
            $refreshCount,
            $refreshLimit
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
        $this->assertSame($token->firstCreatedAt->format('U'), $arr['firstCreatedAt']);
        $this->assertSame($token->refreshCount, $arr['refreshCount']);
        $this->assertSame($token->refreshLimit, $arr['refreshLimit']);
        if ($issuedWithIncluded) {
            $this->assertSame($token->issuedWith, $arr['issuedWith']);
        }
    }

    public static function dataProvider(): array
    {
        return [
            [
                new DateTimeImmutable(),
                new DateTimeImmutable(),
                ['foo'],
                'bar',
                123,
                true,
                false,
                123,
                new DateTimeImmutable(),
                10,
                20,
            ],
            [
                new DateTimeImmutable(),
                new DateTimeImmutable(),
                [],
                'baz',
                234,
                false,
                true,
                null,
                new DateTimeImmutable(),
                30,
                40,
            ],
        ];
    }
}
