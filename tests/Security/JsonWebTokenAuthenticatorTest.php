<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Classes\ServiceTokenUser;
use App\Classes\SessionUserInterface;
use App\Security\JsonWebTokenAuthenticator;
use App\Service\JsonWebTokenManager;
use App\Service\ServiceTokenUserProvider;
use App\Tests\TestCase;
use Mockery as m;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use UnexpectedValueException;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Security\JsonWebTokenAuthenticator::class)]
class JsonWebTokenAuthenticatorTest extends TestCase
{
    protected m\MockInterface $routerMock;
    protected m\MockInterface $jsonWebTokenManagerMock;
    protected m\MockInterface $serviceTokenUserProviderMock;
    protected JsonWebTokenAuthenticator $authenticator;

    public function setUp(): void
    {
        parent::setUp();
        $this->routerMock = m::mock(RouterInterface::class);
        $this->jsonWebTokenManagerMock = m::mock(JsonWebTokenManager::class);
        $this->serviceTokenUserProviderMock = m::mock(ServiceTokenUserProvider::class);
        $this->authenticator = new JsonWebTokenAuthenticator(
            $this->jsonWebTokenManagerMock,
            $this->routerMock,
            $this->serviceTokenUserProviderMock
        );
    }

    public function tearDown(): void
    {
        unset($this->authenticator);
        unset($this->routerMock);
        unset($this->jsonWebTokenManagerMock);
        unset($this->serviceTokenUserProviderMock);
        parent::setUp();
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
        $jwt = 'abcde';
        $request = new Request();
        $request->headers->add(['X-JWT-Authorization' => "Token {$jwt}"]);
        $this->jsonWebTokenManagerMock->shouldReceive('isUserToken')->with($jwt)->andReturnTrue();
        $this->jsonWebTokenManagerMock->shouldReceive('getUserIdFromToken')->andReturn(1);
        $passport = $this->authenticator->authenticate($request);
        $this->assertEquals($jwt, $passport->getAttribute('jwt'));
        $this->assertNull($passport->getAttribute(JsonWebTokenManager::WRITEABLE_SCHOOLS_KEY));
    }

    public function testAuthenticateWithServiceToken(): void
    {
        $jwt = 'abcde';
        $schoolIds = [1, 3, 4];
        $request = new Request();
        $request->headers->add(['X-JWT-Authorization' => "Token {$jwt}"]);
        $this->jsonWebTokenManagerMock->shouldReceive('isUserToken')->andReturnFalse();
        $this->jsonWebTokenManagerMock->shouldReceive('isServiceToken')->andReturnTrue();
        $this->jsonWebTokenManagerMock->shouldReceive('getServiceTokenIdFromToken')->andReturn(1);
        $this->jsonWebTokenManagerMock
            ->shouldReceive('getWriteableSchoolIdsFromToken')
            ->with($jwt)
            ->andReturn($schoolIds);
        $passport = $this->authenticator->authenticate($request);
        $this->assertEquals($jwt, $passport->getAttribute('jwt'));
        $this->assertEquals($schoolIds, $passport->getAttribute(JsonWebTokenManager::WRITEABLE_SCHOOLS_KEY));
    }

    public function testAuthenticateFailsWithoutIdentity(): void
    {
        $jwt = 'abcde';
        $request = new Request();
        $request->headers->add(['X-JWT-Authorization' => "Token {$jwt}"]);
        $this->jsonWebTokenManagerMock->shouldReceive('isUserToken')->andReturnFalse();
        $this->jsonWebTokenManagerMock->shouldReceive('isServiceToken')->andReturnFalse();
        $this->expectException(CustomUserMessageAuthenticationException::class);
        $this->expectExceptionMessage('Invalid JSON Web Token');
        $this->authenticator->authenticate($request);
    }

    public function testAuthenticateFailsWithCorruptedJwt(): void
    {
        $jwt = 'abcde';
        $request = new Request();
        $request->headers->add(['X-JWT-Authorization' => "Token {$jwt}"]);
        $this->jsonWebTokenManagerMock->shouldReceive('isUserToken')->andReturnTrue();
        $this->jsonWebTokenManagerMock
            ->shouldReceive('getUserIdFromToken')
            ->andThrow(new UnexpectedValueException('something went wrong'));
        $this->expectException(CustomUserMessageAuthenticationException::class);
        $this->expectExceptionMessage('Invalid JSON Web Token: something went wrong');
        $this->authenticator->authenticate($request);
    }

    public function testCreateTokenForUser(): void
    {
        $jwt = 'abcde';
        $userMock = m::mock(SessionUserInterface::class);
        $userMock->shouldReceive('getRoles')->andReturn([]);
        $passportMock = m::mock(Passport::class);
        $passportMock->shouldReceive('getAttribute')->with('jwt')->andReturn($jwt);
        $passportMock->shouldReceive('getUser')->andReturn($userMock);
        $this->jsonWebTokenManagerMock->shouldReceive('isServiceToken')->andReturn(false);

        $token = $this->authenticator->createToken($passportMock, 'main');

        $this->assertEquals($jwt, $token->getAttribute('jwt'));
        $this->assertEquals($userMock, $token->getUser());
    }

    public function testCreateTokenForServiceToken(): void
    {
        $jwt = 'abcde';
        $schoolIds = [1, 2, 3];
        $userMock = m::mock(ServiceTokenUser::class);
        $userMock->shouldReceive('getRoles')->andReturn([]);
        $passportMock = m::mock(Passport::class);
        $passportMock->shouldReceive('getAttribute')->with('jwt')->andReturn($jwt);
        $passportMock->shouldReceive('getUser')->andReturn($userMock);
        $this->jsonWebTokenManagerMock->shouldReceive('isServiceToken')->andReturn(true);
        $this->jsonWebTokenManagerMock
            ->shouldReceive('getWriteableSchoolIdsFromToken')
            ->with($jwt)
            ->andReturn($schoolIds);
        $token = $this->authenticator->createToken($passportMock, 'main');

        $this->assertEquals($jwt, $token->getAttribute('jwt'));
        $this->assertEquals($schoolIds, $token->getAttribute(JsonWebTokenManager::WRITEABLE_SCHOOLS_KEY));
        $this->assertEquals($userMock, $token->getUser());
    }
}
