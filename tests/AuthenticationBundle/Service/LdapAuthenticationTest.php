<?php
namespace Tests\AuthenticationBundle\Service;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\Service\JsonWebTokenManager;
use Ilios\CoreBundle\Entity\AuthenticationInterface;
use Ilios\CoreBundle\Entity\Manager\AuthenticationManager;
use Ilios\CoreBundle\Service\Config;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Mockery as m;

use Ilios\AuthenticationBundle\Service\LdapAuthentication;
use Symfony\Component\HttpFoundation\Request;

class LdapAuthenticationTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    protected $authManager;
    protected $jwtManager;
    protected $config;
    protected $obj;

    public function setup()
    {
        $this->authManager = m::mock(AuthenticationManager::class);
        $this->jwtManager = m::mock(JsonWebTokenManager::class);
        $this->config = m::mock(Config::class);
        $this->config->shouldReceive('get')->with('ldap_authentication_host')->andReturn('host');
        $this->config->shouldReceive('get')->with('ldap_authentication_port')->andReturn('port');
        $this->config->shouldReceive('get')->with('ldap_authentication_bind_template')->andReturn('bindTemplate');
        $this->obj = new LdapAuthentication(
            $this->authManager,
            $this->jwtManager,
            $this->config
        );
    }

    public function tearDown()
    {
        unset($this->obj);
        unset($this->authManager);
        unset($this->jwtManager);
        unset($this->config);
    }


    public function testConstructor()
    {
        $this->assertTrue($this->obj instanceof LdapAuthentication);
    }
    
    public function testMissingValues()
    {
        $arr = [
            'username' => null,
            'password' => null
        ];

        $request = m::mock(Request::class);
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
        $arr = [
            'username' => 'abc',
            'password' => '123'
        ];
        $request = m::mock(Request::class);
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
        $obj = m::mock(
            LdapAuthentication::class . '[checkLdapPassword]',
            array(
                $this->authManager,
                $this->jwtManager,
                $this->config
            )
        );
        $obj->shouldReceive('checkLdapPassword')->once()->andReturn(false);
        $arr = [
            'username' => 'abc',
            'password' => '123'
        ];

        $request = m::mock(Request::class);
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));
        $sessionUser = m::mock(SessionUserInterface::class)->shouldReceive('isEnabled')->andReturn(true)->mock();
        $authenticationEntity = m::mock(AuthenticationInterface::class)
            ->shouldReceive('getSessionUser')->andReturn($sessionUser)->mock();
        $this->authManager->shouldReceive('findAuthenticationByUsername')
            ->with('abc')->andReturn($authenticationEntity);
        
        $result = $obj->login($request);

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

        $request = m::mock(Request::class);
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));
        $sessionUser = m::mock(SessionUserInterface::class)->shouldReceive('isEnabled')->andReturn(false)->mock();
        $authenticationEntity = m::mock(AuthenticationInterface::class)
            ->shouldReceive('getSessionUser')->andReturn($sessionUser)->mock();
        $this->authManager->shouldReceive('findAuthenticationByUsername')
            ->with('abc')->andReturn($authenticationEntity);

        $result = $this->obj->login($request);

        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'error');
        $this->assertTrue(in_array('badCredentials', $data->errors));
    }
    
    public function testSuccess()
    {
        //partially mock so we can override checkLdapPassword
        //and not deal with php global ldap functions
        $obj = m::mock(
            LdapAuthentication::class . '[checkLdapPassword]',
            array(
                $this->authManager,
                $this->jwtManager,
                $this->config
            )
        );
        $obj->shouldReceive('checkLdapPassword')->once()->andReturn(true);
        $arr = [
            'username' => 'abc',
            'password' => '123'
        ];

        $request = m::mock(Request::class);
        $request->shouldReceive('getContent')->once()->andReturn(json_encode($arr));

        $sessionUser = m::mock(SessionUserInterface::class)
            ->shouldReceive('isEnabled')->andReturn(true)->mock();
        $authenticationEntity = m::mock(AuthenticationInterface::class)
            ->shouldReceive('getSessionUser')->andReturn($sessionUser)->mock();
        $this->authManager->shouldReceive('findAuthenticationByUsername')
            ->with('abc')->andReturn($authenticationEntity);
        $this->jwtManager->shouldReceive('createJwtFromSessionUser')->with($sessionUser)->andReturn('jwt123Test');

        $result = $obj->login($request);
        
        $this->assertTrue($result instanceof JsonResponse);
        $content = $result->getContent();
        $data = json_decode($content);
        $this->assertSame($data->status, 'success');
        $this->assertSame($data->jwt, 'jwt123Test');
    }
}
