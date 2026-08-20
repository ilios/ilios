<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Classes\Jwt\ServiceToken;
use App\Classes\Jwt\UserToken;
use App\Classes\ServiceTokenUser;
use App\Classes\SessionUserInterface;
use App\Security\JsonWebTokenAuthenticator;
use App\Service\Jwt\TokenCodec;
use App\Service\Jwt\TokenFactory;
use App\Service\SecretManager;
use App\Service\ServiceTokenUserProvider;
use App\Tests\TestCase;
use DateInterval;
use DateTimeImmutable;
use Mockery as m;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

#[CoversClass(JsonWebTokenAuthenticator::class)]
final class JsonWebTokenAuthenticatorTest extends TestCase
{
    protected m\MockInterface $routerMock;
    protected m\MockInterface $serviceTokenUserProviderMock;
    protected TokenCodec $tokenCodec;
    protected TokenFactory $tokenFactory;
    protected JsonWebTokenAuthenticator $authenticator;

    public function setUp(): void
    {
        parent::setUp();
        $this->tokenCodec = new TokenCodec(
            new SecretManager(
                'sadfadsfASDFASFASDGF ADSFG adsfASDF',
                'sdfasdATR AGADS FGadsf ASDRFARDA ',
            )
        );
        $this->tokenFactory = new TokenFactory();
        $this->routerMock = m::mock(RouterInterface::class);
        $this->serviceTokenUserProviderMock = m::mock(ServiceTokenUserProvider::class);
        $this->authenticator = new JsonWebTokenAuthenticator(
            $this->tokenCodec,
            $this->tokenFactory,
            $this->routerMock,
            $this->serviceTokenUserProviderMock
        );
    }

    public function tearDown(): void
    {
        unset($this->tokenCodec);
        unset($this->tokenFactory);
        unset($this->routerMock);
        unset($this->serviceTokenUserProviderMock);
        unset($this->authenticator);
        parent::tearDown();
    }

    public function testSupports(): void
    {
        $request = new Request();
        $request->headers->add(['X-JWT-Authorization' => 'Token abcde']);
        $this->assertTrue($this->authenticator->supports($request));
    }

    public function testSupportsFailsWithoutXHeader(): void
    {
        $request = new Request();
        $this->assertFalse($this->authenticator->supports($request));
    }

    public function testSupportsFailsWithInvalidTokenInHeader(): void
    {
        $request = new Request();
        $request->headers->add(['X-JWT-Authorization' => 'gibberish']);
        $this->assertFalse($this->authenticator->supports($request));
    }

    public function testOnAuthenticationFailure(): void
    {
        $exception = new AuthenticationException('lorem ipsum');
        $response = $this->authenticator->onAuthenticationFailure(
            m::mock(Request::class),
            $exception
        );
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertEquals('Authentication Failed. lorem ipsum', $response->getContent());
    }

    public function testOnAuthenticationSuccess(): void
    {
        $response = $this->authenticator->onAuthenticationSuccess(
            m::mock(Request::class),
            m::mock(TokenInterface::class),
            'default'
        );
        $this->assertNull($response);
    }

    public function testAuthenticateWithUserToken(): void
    {
        $jwt = $this->tokenCodec->encode([
                'iat' => new DateTimeImmutable()->format('U'),
                'exp' => new DateTimeImmutable()->add(new DateInterval('PT40M'))->format('U'),
                'iss' => 'whoever',
                'aud' => 'lorem ipsum',
                'user_id' => 1,
        ]);
        $request = new Request();
        $request->headers->add(['X-JWT-Authorization' => "Token {$jwt}"]);
        $passport = $this->authenticator->authenticate($request);
        $token = $passport->getAttribute('token');
        $this->assertInstanceOf(UserToken::class, $token);
    }

    public function testAuthenticateWithServiceToken(): void
    {
        $jwt = $this->tokenCodec->encode([
            'iat' => new DateTimeImmutable()->format('U'),
            'exp' => new DateTimeImmutable()->add(new DateInterval('PT40M'))->format('U'),
            'iss' => 'whoever',
            'aud' => 'lorem ipsum',
            'token_id' => 1,
        ]);
        $request = new Request();
        $request->headers->add(['X-JWT-Authorization' => "Token {$jwt}"]);
        $passport = $this->authenticator->authenticate($request);
        $token = $passport->getAttribute('token');
        $this->assertInstanceOf(ServiceToken::class, $token);
    }

    public function testAuthenticateFailsWithoutIdentity(): void
    {
        $jwt = $this->tokenCodec->encode([
            'iat' => new DateTimeImmutable()->format('U'),
            'exp' => new DateTimeImmutable()->add(new DateInterval('PT40M'))->format('U'),
            'iss' => 'whoever',
            'aud' => 'lorem ipsum',
        ]);
        $request = new Request();
        $request->headers->add(['X-JWT-Authorization' => "Token {$jwt}"]);
        $this->expectException(CustomUserMessageAuthenticationException::class);
        $this->expectExceptionMessage('Invalid JSON Web Token');
        $this->authenticator->authenticate($request);
    }

    public function testAuthenticateFailsWithCorruptedJwt(): void
    {
        $codec = new TokenCodec(
            new SecretManager('a different secret to encode this token with', '')
        );
        $jwt = $codec->encode([
            'iat' => new DateTimeImmutable()->format('U'),
            'exp' => new DateTimeImmutable()->add(new DateInterval('PT40M'))->format('U'),
            'iss' => 'whoever',
            'aud' => 'lorem ipsum',
        ]);
        $request = new Request();
        $request->headers->add(['X-JWT-Authorization' => "Token {$jwt}"]);
        $this->expectException(CustomUserMessageAuthenticationException::class);
        $this->expectExceptionMessage('Invalid JSON Web Token: Signature verification failed');
        $this->authenticator->authenticate($request);
    }

    public function testCreateUserToken(): void
    {
        $token = $this->tokenFactory->create([
            'iat' => new DateTimeImmutable()->format('U'),
            'exp' => new DateTimeImmutable()->add(new DateInterval('PT40M'))->format('U'),
            'iss' => 'whoever',
            'aud' => 'lorem ipsum',
            'user_id' => 100,
        ]);
        $userMock = m::mock(SessionUserInterface::class);
        $userMock->shouldReceive('getRoles')->andReturn([]);
        $passportMock = m::mock(Passport::class);
        $passportMock->shouldReceive('getAttribute')->with('token')->andReturn($token);
        $passportMock->shouldReceive('getUser')->andReturn($userMock);

        $securityToken = $this->authenticator->createToken($passportMock, 'main');

        $this->assertSame($token, $securityToken->getAttribute('token'));
        $this->assertSame($userMock, $securityToken->getUser());
    }

    public function testCreateTokenForServiceToken(): void
    {
        $token = $this->tokenFactory->create([
            'iat' => new DateTimeImmutable()->format('U'),
            'exp' => new DateTimeImmutable()->add(new DateInterval('PT40M'))->format('U'),
            'iss' => 'whoever',
            'aud' => 'lorem ipsum',
            'user_id' => 100,
        ]);
        $userMock = m::mock(ServiceTokenUser::class);
        $userMock = m::mock(SessionUserInterface::class);
        $userMock->shouldReceive('getRoles')->andReturn([]);
        $passportMock = m::mock(Passport::class);
        $passportMock->shouldReceive('getAttribute')->with('token')->andReturn($token);
        $passportMock->shouldReceive('getUser')->andReturn($userMock);

        $securityToken = $this->authenticator->createToken($passportMock, 'main');

        $this->assertSame($token, $securityToken->getAttribute('token'));
        $this->assertSame($userMock, $securityToken->getUser());
    }
}
