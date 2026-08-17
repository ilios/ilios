<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Classes\ServiceToken;
use App\Classes\UserToken;
use App\Service\SecretManager;
use App\Service\TokenCodec;
use App\Service\TokenFactory;
use App\Tests\TestCase;
use DateInterval;
use DateTimeImmutable;
use DomainException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(TokenFactory::class)]
final class TokenFactoryTest extends TestCase
{
    protected TokenFactory $factory;

    protected TokenCodec $codec;

    protected function setUp(): void
    {
        parent::setUp();
        $secretManager = new SecretManager(
            'TheMoreYouIgnoreMeTheCloserIGet',
            'BeerDrinkersAndHellRaisers',
        );
        $this->codec = new TokenCodec($secretManager);
        $this->factory = new TokenFactory();
    }

    protected function tearDown(): void
    {
        unset($this->factory);
        unset($this->codec);
        parent::tearDown();
    }


    #[DataProvider('createUserTokenProvider')]
    public function testCreateUserToken(array $input, UserToken $expectedToken): void
    {
        $token = $this->factory->create($input);
        $this->assertUserTokenEquals($expectedToken, $token);
    }


    #[DataProvider('createServiceTokenProvider')]
    public function testCreateServiceToken(array $input, ServiceToken $expectedToken): void
    {
        $token = $this->factory->create($input);
        $this->assertServiceTokenEquals($expectedToken, $token);
    }

    public function testTokenCreationFailsIfTypeCannotBeDetermined(): void
    {
        $input = [];
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageIsOrContains('Unable to determine token type.');
        $this->factory->create($input);
    }

    #[DataProvider('tokenCreationFailsOnMissingAttributesProvider')]
    public function testTokenCreationFailsOnMissingAttributes(array $input, string $expectedErrorMessage): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessageIsOrContains($expectedErrorMessage);
        $this->factory->create($input);
    }

    public static function createUserTokenProvider(): array
    {
        $issuedAt = new DateTimeImmutable();
        $expiresAt = $issuedAt->add(new DateInterval('PT8H'));
        $firstIssuedAt = $issuedAt->sub(new DateInterval('P10D'));
        return [
            [
                [
                    'iat' => $issuedAt->format('U'),
                    'exp' => $expiresAt->format('U'),
                    'aud' => ['bob'],
                    'iss' => 'fizz',
                    'user_id' => 1,
                ],
                new UserToken(
                    $issuedAt,
                    $expiresAt,
                    ['bob'],
                    'fizz',
                    1,
                    false,
                    false,
                    null,
                    $issuedAt,
                    0,
                ),
            ],
            [
             [
                 'iat' => $issuedAt->format('U'),
                    'exp' => $expiresAt->format('U'),
                    'aud' => ['foo'],
                    'iss' => 'bar',
                    'user_id' => 2,
                    'is_root' => true,
                    'performs_non_learner_function' => false,
                    'issued_with' => 123,
                    'firstCreatedAt' => $firstIssuedAt->format('U'),
                    'refreshCount' => 10,
                ],
                new UserToken(
                    $issuedAt,
                    $expiresAt,
                    ['foo'],
                    'bar',
                    2,
                    true,
                    false,
                    123,
                    $firstIssuedAt,
                    10,
                ),
            ],
            [
                [
                    'iat' => $issuedAt->format('U'),
                    'exp' => $expiresAt->format('U'),
                    'aud' => 'foo',
                    'iss' => 'bar',
                    'user_id' => 3,
                    'is_root' => false,
                    'performs_non_learner_function' => true,
                    'issued_with' => 234,
                    'firstCreatedAt' => $firstIssuedAt->format('U'),
                    'refreshCount' => 11,
                ],
                new UserToken(
                    $issuedAt,
                    $expiresAt,
                    ['foo'],
                    'bar',
                    3,
                    false,
                    true,
                    234,
                    $firstIssuedAt,
                    11,
                ),
            ],
        ];
    }

    public static function createServiceTokenProvider(): array
    {
        $issuedAt = new DateTimeImmutable();
        $expiresAt = $issuedAt->add(new DateInterval('PT8H'));
        return [
            [
                [
                    'iat' => $issuedAt->format('U'),
                    'exp' => $expiresAt->format('U'),
                    'aud' => ['foo'],
                    'iss' => 'bar',
                    'token_id' => 1,
                ],
                new ServiceToken(
                    $issuedAt,
                    $expiresAt,
                    ['foo'],
                    'bar',
                    1,
                    [],
                    false,
                    false,
                ),
            ],
            [
                [
                    'iat' => $issuedAt->format('U'),
                    'exp' => $expiresAt->format('U'),
                    'aud' => ['bob'],
                    'iss' => 'fizz',
                    'token_id' => 2,
                    'writeable_schools' => [1, 2, 3],
                    'can_create_or_update_user_in_any_school' => true,
                    'can_generate_user_tokens' => false,
                ],
                new ServiceToken(
                    $issuedAt,
                    $expiresAt,
                    ['bob'],
                    'fizz',
                    2,
                    [1, 2, 3],
                    true,
                    false,
                ),
            ],
            [
                [
                    'iat' => $issuedAt->format('U'),
                    'exp' => $expiresAt->format('U'),
                    'aud' => 'bob',
                    'iss' => 'fizz',
                    'token_id' => 3,
                    'writeable_schools' => [],
                    'can_create_or_update_user_in_any_school' => false,
                    'can_generate_user_tokens' => true,
                ],
                new ServiceToken(
                    $issuedAt,
                    $expiresAt,
                    ['bob'],
                    'fizz',
                    3,
                    [],
                    false,
                    true,
                ),
            ],
        ];
    }

    public static function tokenCreationFailsOnMissingAttributesProvider(): array
    {
        return [
            [
                ['user_id' => 1],
                'Token is missing the mandatory attributes "iat, exp, aud, iss".',
            ],
            [
                ['user_id' => 1, 'iat' => new DateTimeImmutable()],
                'Token is missing the mandatory attributes "exp, aud, iss".',
            ],
            [
                ['user_id' => 1, 'exp' => new DateTimeImmutable()->add(new DateInterval('PT8H'))],
                'Token is missing the mandatory attributes "iat, aud, iss".',
            ],
            [
                ['user_id' => 1, 'aud' => 'ilios'],
                'Token is missing the mandatory attributes "iat, exp, iss".',
            ],
            [
                ['user_id' => 1, 'iss' => 'ilios'],
                'Token is missing the mandatory attributes "iat, exp, aud".',
            ],
        ];
    }

    protected function assertUserTokenEquals(UserToken $expectedToken, UserToken $token): void
    {
        $this->assertSame($expectedToken->issuedAt->format('U'), $token->issuedAt->format('U'));
        $this->assertSame($expectedToken->expiresAt->format('U'), $token->expiresAt->format('U'));
        $this->assertSame($expectedToken->audience, $token->audience);
        $this->assertSame($expectedToken->issuer, $token->issuer);
        $this->assertSame($expectedToken->userId, $token->userId);
        $this->assertSame($expectedToken->isRoot, $token->isRoot);
        $this->assertSame($expectedToken->performsNonLearnerFunction, $token->performsNonLearnerFunction);
        $this->assertSame($expectedToken->issuedWith, $token->issuedWith);
        $this->assertSame($expectedToken->firstCreatedAt->format('U'), $token->firstCreatedAt->format('U'));
        $this->assertSame($expectedToken->refreshCount, $token->refreshCount);
    }

    protected function assertServiceTokenEquals(ServiceToken $expectedToken, ServiceToken $token): void
    {
        $this->assertSame($expectedToken->issuedAt->format('U'), $token->issuedAt->format('U'));
        $this->assertSame($expectedToken->expiresAt->format('U'), $token->expiresAt->format('U'));
        $this->assertSame($expectedToken->audience, $token->audience);
        $this->assertSame($expectedToken->issuer, $token->issuer);
        $this->assertSame($expectedToken->serviceTokenId, $token->serviceTokenId);
        $this->assertSame($expectedToken->writeableSchoolIds, $token->writeableSchoolIds);
        $this->assertSame($expectedToken->canCreateOrUpdateUserInAnySchool, $token->canCreateOrUpdateUserInAnySchool);
        $this->assertSame($expectedToken->canCreateUserTokensFromToken, $token->canCreateUserTokensFromToken);
    }
}
