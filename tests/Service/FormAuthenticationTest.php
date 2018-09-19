<?php
namespace Tests\App\Service;

use App\Classes\SessionUserInterface;
use App\Service\JsonWebTokenManager;
use App\Service\SessionUserProvider;
use App\Entity\Manager\AuthenticationManager;
use App\Entity\Manager\UserManager;
use App\Entity\UserInterface;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Mockery as m;

use App\Service\FormAuthentication;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FormAuthenticationTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    protected $authManager;
    protected $userManager;
    protected $encoder;
    protected $tokenStorage;
    protected $jwtManager;
    protected $sessionUserProvider;

    /**
     * @var FormAuthentication
     */
    protected $obj;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->authManager = m::mock(AuthenticationManager::class);
        $this->encoder = m::mock(UserPasswordEncoderInterface::class);
        $this->tokenStorage = m::mock(TokenStorageInterface::class);
        $this->jwtManager = m::mock(JsonWebTokenManager::class);
        $this->sessionUserProvider = m::mock(SessionUserProvider::class);
        $this->userManager = m::mock(UserManager::class);
        $this->obj = new FormAuthentication(
            $this->authManager,
            $this->userManager,
            $this->encoder,
            $this->tokenStorage,
            $this->jwtManager,
            $this->sessionUserProvider
        );
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        unset($this->authManager);
        unset($this->userManager);
        unset($this->encoder);
        unset($this->tokenStorage);
        unset($this->jwtManager);
        unset($this->sessionUserProvider);
        unset($this->obj);
    }

    public function testConstructor()
    {
        $this->assertTrue($this->obj instanceof FormAuthentication);
    }

    public function testMissingValues()
    {
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $arr = [
            'username' => null,
            'password' => null
        ];
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $result = $this->obj->login($request);

        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('missingUsername', $data->errors));
        $this->assertTrue(in_array('missingPassword', $data->errors));
    }

    public function testBadUserName()
    {
        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $arr = [
            'username' => 'abc',
            'password' => '123'
        ];
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $this->authManager->shouldReceive('findAuthenticationByUsername')
            ->with('abc')->andReturn(null);
        $result = $this->obj->login($request);

        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('badCredentials', $data->errors));
    }

    public function testBadPassword()
    {
        $arr = [
            'username' => 'abc',
            'password' => '123'
        ];

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $user = m::mock(UserInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class)
            ->shouldReceive('isEnabled')->andReturn(true)->mock();
        $authenticationEntity = m::mock('App\Entity\AuthenticationInterface')
            ->shouldReceive('getUser')->andReturn($user)->mock();
        $this->authManager->shouldReceive('findAuthenticationByUsername')
            ->with('abc')->andReturn($authenticationEntity);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);
        $this->encoder->shouldReceive('isPasswordValid')->with($sessionUser, '123')->andReturn(false);
        $result = $this->obj->login($request);

        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('badCredentials', $data->errors));
    }

    public function testDisabledUser()
    {
        $arr = [
            'username' => 'abc',
            'password' => '123'
        ];

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $user = m::mock(UserInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class)
            ->shouldReceive('isEnabled')->andReturn(false)->mock();
        $authenticationEntity = m::mock('App\Entity\AuthenticationInterface')
            ->shouldReceive('getUser')->andReturn($user)->mock();
        $this->authManager->shouldReceive('findAuthenticationByUsername')
            ->with('abc')->andReturn($authenticationEntity);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);
        $result = $this->obj->login($request);

        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('badCredentials', $data->errors));
    }

    public function testSuccess()
    {
        $arr = [
            'username' => 'abc',
            'password' => '123'
        ];

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $user = m::mock(UserInterface::class);
        $sessionUser = m::mock(SessionUserInterface::class)
            ->shouldReceive('isEnabled')->andReturn(true)->mock();
        $authenticationEntity = m::mock('App\Entity\AuthenticationInterface')
            ->shouldReceive('getUser')->andReturn($user)
            ->shouldReceive('isLegacyAccount')->andReturn(false)->mock();
        $this->authManager->shouldReceive('findAuthenticationByUsername')
            ->with('abc')->andReturn($authenticationEntity);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);
        $this->encoder->shouldReceive('isPasswordValid')->with($sessionUser, '123')->andReturn(true);
        $this->jwtManager->shouldReceive('createJwtFromSessionUser')->with($sessionUser)->andReturn('jwt123Test');

        $result = $this->obj->login($request);

        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertSame($data->jwt, 'jwt123Test');
    }
}
