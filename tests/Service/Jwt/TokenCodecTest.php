<?php

declare(strict_types=1);

namespace App\Tests\Service\Jwt;

use App\Classes\Jwt\ServiceToken;
use App\Classes\Jwt\UserToken;
use App\Service\Jwt\TokenCodec;
use App\Service\SecretManager;
use App\Tests\TestCase;
use DateTimeImmutable;
use Firebase\JWT\SignatureInvalidException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TokenCodec::class)]
final class TokenCodecTest extends TestCase
{
    protected TokenCodec $codec;

    protected SecretManager $secretManager;
    protected function setUp(): void
    {
        parent::setUp();
        $this->secretManager = new SecretManager(
            'VomEiseBefreitSindStromUndBaeche',
            'AbandonHopeAllYeWhoEnterHere',
        );
        $this->codec = new TokenCodec($this->secretManager);
    }

    protected function tearDown(): void
    {
        unset($this->codec);
        unset($this->secretManager);
        parent::tearDown();
    }

    public function testDecode(): void
    {
        $data = ['foo' => 'bar'];
        $jwt = $this->codec->encode($data);
        $decodedData = $this->codec->decode($jwt);
        $this->assertEquals($decodedData, $data);
    }

    public function testDecodeWithTransitionalSecret(): void
    {
        $data = ['foo' => 'bar'];
        $transitionalSecret = $this->secretManager->getTransitionalSecret();
        $otherCodec = new TokenCodec(
            new SecretManager($transitionalSecret, 'loremipsum')
        );
        $jwt = $otherCodec->encode($data);
        $decodedData = $this->codec->decode($jwt);
        $this->assertEquals($decodedData, $data);
    }

    public function testDecodeFailsWithInvalidSignature(): void
    {
        $data = ['foo', 'bar'];
        $otherCodec = new TokenCodec(
            new SecretManager(
                $this->secretManager->getSecret() . '2',
                'loremipsum'
            )
        );
        $jwt = $otherCodec->encode($data);

        $this->expectException(SignatureInvalidException::class);
        $this->codec->decode($jwt);
    }

    public function testEncodeArray(): void
    {
        $data = ['foo' => 'bar'];
        $jwt = $this->codec->encode($data);
        $decodedData = $this->codec->decode($jwt);
        $this->assertEquals($decodedData, $data);
    }

    public function testEncodeUserToken(): void
    {
        $issuedAt = new DateTimeImmutable();
        $expiresAt = new DateTimeImmutable();
        $audience = ['foo', 'bar'];
        $issuer = 'lorem';
        $userId = 123;
        $isRoot = true;
        $performsNonLearnerFunction = true;
        $canCreateOrUpdateUserInAnySchool = true;
        $issuedWith = 10;
        $firstCreatedAt =  new DateTimeImmutable();
        $refreshCount = 20;
        $userToken = new UserToken(
            $issuedAt,
            $expiresAt,
            $audience,
            $issuer,
            $userId,
            $isRoot,
            $performsNonLearnerFunction,
            $canCreateOrUpdateUserInAnySchool,
            $issuedWith,
            $firstCreatedAt,
            $refreshCount,
        );
        $jwt = $this->codec->encode($userToken);
        $decodedData = $this->codec->decode($jwt);
        $this->assertSame($issuedAt->format('U'), $decodedData['iat']);
        $this->assertSame($expiresAt->format('U'), $decodedData['exp']);
        $this->assertSame($audience, $decodedData['aud']);
        $this->assertSame($issuer, $decodedData['iss']);
        $this->assertSame($isRoot, $decodedData['is_root']);
        $this->assertSame($performsNonLearnerFunction, $decodedData['performs_non_learner_function']);
        $this->assertSame($canCreateOrUpdateUserInAnySchool, $decodedData['can_create_or_update_user_in_any_school']);
        $this->assertSame($issuedWith, $decodedData['issued_with']);
        $this->assertSame($firstCreatedAt->format('U'), $decodedData['firstCreatedAt']);
        $this->assertSame($refreshCount, $decodedData['refreshCount']);
    }

    public function testEncodeServiceToken(): void
    {
        $issuedAt = new DateTimeImmutable();
        $expiresAt = new DateTimeImmutable();
        $audience = ['foo', 'bar'];
        $issuer = 'lorem';
        $serviceTokenId = 5;
        $writeableSchoolIds = [1, 2 ,3];
        $canCreateUserTokensFromToken = true;
        $userToken = new ServiceToken(
            $issuedAt,
            $expiresAt,
            $audience,
            $issuer,
            $serviceTokenId,
            $writeableSchoolIds,
            $canCreateUserTokensFromToken
        );
        $jwt = $this->codec->encode($userToken);
        $decodedData = $this->codec->decode($jwt);
        $this->assertSame($issuedAt->format('U'), $decodedData['iat']);
        $this->assertSame($expiresAt->format('U'), $decodedData['exp']);
        $this->assertSame($audience, $decodedData['aud']);
        $this->assertSame($issuer, $decodedData['iss']);
        $this->assertSame($serviceTokenId, $decodedData['token_id']);
        $this->assertSame($writeableSchoolIds, $decodedData['writeable_schools']);
        $this->assertSame($canCreateUserTokensFromToken, $decodedData['can_generate_user_tokens']);
    }
}
