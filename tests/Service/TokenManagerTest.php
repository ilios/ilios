<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Classes\ServiceToken;
use App\Classes\UserToken;
use App\Service\SecretManager;
use App\Service\TokenCodec;
use App\Service\TokenFactory;
use App\Service\TokenManager;
use App\Tests\TestCase;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(TokenManager::class)]
final class TokenManagerTest extends TestCase
{
    protected TokenCodec $codec;
    protected TokenFactory $factory;
    protected TokenManager $manager;

    public function setUp(): void
    {
        parent::setUp();
        $this->codec = new TokenCodec(new SecretManager('TheSignOfTheSouthernCross', 'ChildrenOfTheSea'));
        $this->factory = new TokenFactory();
        $this->manager = new TokenManager($this->codec, $this->factory);
    }

    public function tearDown(): void
    {
        unset($this->manager);
        unset($this->codec);
        unset($this->factory);
        parent::tearDown();
    }

    #[DataProvider('extractFromJwtProvider')]
    public function testExtractServiceTokenFromJwt(array $input, string $expectedTokenType): void
    {
        $jwt = $this->codec->encode($input);
        $token = $this->manager->extractJwt($jwt);
        $this->assertInstanceOf($expectedTokenType, $token);
    }

    public static function extractFromJwtProvider(): array
    {
        return [
            [
                [
                    'iat' => new DateTimeImmutable()->format('U'),
                    'exp' => new DateTimeImmutable()->add(new DateInterval('PT8H'))->format('U'),
                    'aud' => ['foo'],
                    'iss' => 'bar',
                    'user_id' => 1,
                ],
                UserToken::class,
            ],
            [
                [
                    'iat' => new DateTimeImmutable()->format('U'),
                    'exp' => new DateTimeImmutable()->add(new DateInterval('PT8H'))->format('U'),
                    'aud' => ['foo'],
                    'iss' => 'bar',
                    'token_id' => 1,
                ],
                ServiceToken::class,
            ],
        ];
    }
}
