<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Classes\ServiceToken;
use App\Classes\UserToken;
use App\Service\SecretManager;
use App\Service\TokenCodec;
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
        $audience = ['foo'];
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
        $this->assertSame($decodedData['iat'], $issuedAt->format('U'));
        $this->assertSame($decodedData['exp'], $expiresAt->format('U'));
        $this->assertSame($decodedData['aud'], $audience);
        $this->assertSame($decodedData['iss'], $issuer);
        $this->assertSame($decodedData['is_root'], $isRoot);
        $this->assertSame($decodedData['performs_non_learner_function'], $performsNonLearnerFunction);
        $this->assertSame($decodedData['can_create_or_update_user_in_any_school'], $canCreateOrUpdateUserInAnySchool);
        $this->assertSame($decodedData['issued_with'], $issuedWith);
        $this->assertSame($decodedData['firstCreatedAt'], $firstCreatedAt->format('U'));
        $this->assertSame($decodedData['refreshCount'], $refreshCount);
    }

    public function testEncodeServiceToken(): void
    {
        $issuedAt = new DateTimeImmutable();
        $expiresAt = new DateTimeImmutable();
        $audience = ['foo'];
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
        $this->assertSame($decodedData['iat'], $issuedAt->format('U'));
        $this->assertSame($decodedData['exp'], $expiresAt->format('U'));
        $this->assertSame($decodedData['aud'], $audience);
        $this->assertSame($decodedData['iss'], $issuer);
        $this->assertSame($decodedData['token_id'], $serviceTokenId);
        $this->assertSame($decodedData['writeable_schools'], $writeableSchoolIds);
        $this->assertSame($decodedData['can_generate_user_tokens'], $canCreateUserTokensFromToken);
    }
}
