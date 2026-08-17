<?php

declare(strict_types=1);

namespace App\Tests\Classes;

use App\Classes\ServiceToken;
use App\Tests\TestCase;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(ServiceToken::class)]
final class ServiceTokenTest extends TestCase
{
    #[DataProvider('dataProvider')]
    public function testConstructor(
        DateTimeImmutable $iat,
        DateTimeImmutable $exp,
        array $aud,
        string $iss,
        int $tokenId,
        array $schoolIds,
        bool $canCreateUserTokens,
    ): void {
        $token = new ServiceToken(
            $iat,
            $exp,
            $aud,
            $iss,
            $tokenId,
            $schoolIds,
            $canCreateUserTokens,
        );
        $this->assertSame($iat->format('U'), $token->issuedAt->format('U'));
        $this->assertSame($exp->format('U'), $token->expiresAt->format('U'));
        $this->assertSame($aud, $token->audience);
        $this->assertSame($iss, $token->issuer);
        $this->assertSame($tokenId, $token->serviceTokenId);
        $this->assertSame($schoolIds, $token->writeableSchoolIds);
        $this->assertSame($canCreateUserTokens, $token->canCreateUserTokensFromToken);
    }

    #[DataProvider('dataProvider')]
    public function testToArray(
        DateTimeImmutable $iat,
        DateTimeImmutable $exp,
        array $aud,
        string $iss,
        int $tokenId,
        array $schoolIds,
        bool $canCreateUserTokens,
    ): void {
        $token = new ServiceToken(
            $iat,
            $exp,
            $aud,
            $iss,
            $tokenId,
            $schoolIds,
            $canCreateUserTokens
        );
        $arr = $token->toArray();
        $this->assertCount(7, $arr);
        $this->assertSame($token->issuedAt->format('U'), $arr['iat']);
        $this->assertSame($token->expiresAt->format('U'), $arr['exp']);
        $this->assertSame($token->audience, $arr['aud']);
        $this->assertSame($token->issuer, $arr['iss']);
        $this->assertSame($token->serviceTokenId, $arr['token_id']);
        $this->assertSame($token->writeableSchoolIds, $arr['writeable_schools']);
        $this->assertSame($token->canCreateUserTokensFromToken, $arr['can_generate_user_tokens']);
    }

    public function testToArrayWithSingleItemAudienceArray(): void
    {
        $aud = 'just_this';
        $token = new ServiceToken(
            new DateTimeImmutable(),
            new DateTimeImmutable()->add(new DateInterval('PT5M')),
            [$aud],
            'foo',
            1,
            [],
            false,
        );
        $arr = $token->toArray();
        $this->assertSame($aud, $arr['aud']);
    }

    public static function dataProvider(): array
    {
        return [
            [new DateTimeImmutable(), new DateTimeImmutable(), ['foo', 'bar'], 'bar', 123, [1, 2, 3], true],
            [new DateTimeImmutable(), new DateTimeImmutable(), [], 'baz', 234, [], false],
        ];
    }
}
