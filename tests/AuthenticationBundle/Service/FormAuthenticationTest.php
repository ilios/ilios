<?php
namespace Tests\AuthenticationBundle\Service;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Mockery as m;

use Ilios\AuthenticationBundle\Service\FormAuthentication;

class FormAuthenticationTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function testConstructor()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $encoder = m::mock('Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface');
        $tokenStorage = m::mock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $obj = new FormAuthentication(
            $authManager,
            $encoder,
            $tokenStorage,
            $jwtManager
        );
        $this->assertTrue($obj instanceof FormAuthentication);
    }
    
    public function testMissingValues()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $encoder = m::mock('Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface');
        $tokenStorage = m::mock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $obj = new FormAuthentication(
            $authManager,
            $encoder,
            $tokenStorage,
            $jwtManager
        );

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $arr = [
            'username' => null,
            'password' => null
        ];
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));
        
        $result = $obj->login($request);
        
        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('missingUsername', $data->errors));
        $this->assertTrue(in_array('missingPassword', $data->errors));
    }
    
    public function testBadUserName()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $encoder = m::mock('Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface');
        $tokenStorage = m::mock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $obj = new FormAuthentication(
            $authManager,
            $encoder,
            $tokenStorage,
            $jwtManager
        );

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $arr = [
            'username' => 'abc',
            'password' => '123'
        ];
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $authManager->shouldReceive('findAuthenticationByUsername')
            ->with('abc')->andReturn(null);
        $result = $obj->login($request);
        
        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('badCredentials', $data->errors));
    }
    
    public function testBadPassword()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $encoder = m::mock('Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface');
        $tokenStorage = m::mock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $obj = new FormAuthentication(
            $authManager,
            $encoder,
            $tokenStorage,
            $jwtManager
        );

        $arr = [
            'username' => 'abc',
            'password' => '123'
        ];

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $sessionUser = m::mock('Ilios\AuthenticationBundle\Classes\SessionUserInterface')
            ->shouldReceive('isEnabled')->andReturn(true)->mock();
        $authenticationEntity = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('getSessionUser')->andReturn($sessionUser)->mock();
        $authManager->shouldReceive('findAuthenticationByUsername')
            ->with('abc')->andReturn($authenticationEntity);
        $encoder->shouldReceive('isPasswordValid')->with($sessionUser, '123')->andReturn(false);
        $result = $obj->login($request);
        
        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('badCredentials', $data->errors));
    }

    public function testDisabledUser()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $encoder = m::mock('Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface');
        $tokenStorage = m::mock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $obj = new FormAuthentication(
            $authManager,
            $encoder,
            $tokenStorage,
            $jwtManager
        );

        $arr = [
            'username' => 'abc',
            'password' => '123'
        ];

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $sessionUser = m::mock('Ilios\AuthenticationBundle\Classes\SessionUserInterface')
            ->shouldReceive('isEnabled')->andReturn(false)->mock();
        $authenticationEntity = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('getSessionUser')->andReturn($sessionUser)->mock();
        $authManager->shouldReceive('findAuthenticationByUsername')
            ->with('abc')->andReturn($authenticationEntity);
        $result = $obj->login($request);

        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('badCredentials', $data->errors));
    }
    
    public function testSuccess()
    {
        $authManager = m::mock('Ilios\CoreBundle\Entity\Manager\AuthenticationManager');
        $encoder = m::mock('Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface');
        $tokenStorage = m::mock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
        $jwtManager = m::mock('Ilios\AuthenticationBundle\Service\JsonWebTokenManager');
        $obj = new FormAuthentication(
            $authManager,
            $encoder,
            $tokenStorage,
            $jwtManager
        );

        $arr = [
            'username' => 'abc',
            'password' => '123'
        ];

        $request = m::mock('Symfony\Component\HttpFoundation\Request');
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $sessionUser = m::mock('Ilios\AuthenticationBundle\Classes\SessionUserInterface')
            ->shouldReceive('isEnabled')->andReturn(true)->mock();
        $authenticationEntity = m::mock('Ilios\CoreBundle\Entity\AuthenticationInterface')
            ->shouldReceive('getSessionUser')->andReturn($sessionUser)
            ->shouldReceive('isLegacyAccount')->andReturn(false)->mock();
        $authManager->shouldReceive('findAuthenticationByUsername')
            ->with('abc')->andReturn($authenticationEntity);
        $encoder->shouldReceive('isPasswordValid')->with($sessionUser, '123')->andReturn(true);
        $jwtManager->shouldReceive('createJwtFromSessionUser')->with($sessionUser)->andReturn('jwt123Test');
        
        
        $result = $obj->login($request);
        
        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertSame($data->jwt, 'jwt123Test');
    }
}
