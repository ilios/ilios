<?php
namespace Tests\AuthenticationBundle\Service;

use Ilios\AuthenticationBundle\Service\CasAuthentication;
use Ilios\AuthenticationBundle\Service\FormAuthentication;
use Ilios\AuthenticationBundle\Service\LdapAuthentication;
use Ilios\AuthenticationBundle\Service\ShibbolethAuthentication;
use Ilios\CoreBundle\Service\Config;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Mockery as m;

use Ilios\AuthenticationBundle\Service\AuthenticationFactory;

class AuthenticationFactoryTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    protected $config;
    protected $cas;
    protected $form;
    protected $ldap;
    protected $shib;
    protected $obj;

    public function setup()
    {
        $this->config = m::mock(Config::class);
        $this->cas = m::mock(CasAuthentication::class);
        $this->form   = m::mock(FormAuthentication::class);
        $this->ldap = m::mock(LdapAuthentication::class);
        $this->shib = m::mock(ShibbolethAuthentication::class);
        $this->obj = new AuthenticationFactory(
            $this->config,
            $this->cas,
            $this->form,
            $this->ldap,
            $this->shib
        );
    }

    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        unset($this->obj);
        unset($this->config);
        unset($this->cas);
        unset($this->form);
        unset($this->ldap);
        unset($this->shib);
    }

    public function testCreateCasService()
    {
        $this->config->shouldReceive('get')->once()->with('authentication_type')->andReturn('cas');
        
        $service = $this->obj->createAuthenticationService();
        $this->assertSame($this->cas, $service);
    }

    public function testCreateFormService()
    {
        $this->config->shouldReceive('get')->once()->with('authentication_type')->andReturn('form');

        $service = $this->obj->createAuthenticationService();
        $this->assertSame($this->form, $service);
    }

    public function testCreateLdapService()
    {
        $this->config->shouldReceive('get')->once()->with('authentication_type')->andReturn('ldap');

        $service = $this->obj->createAuthenticationService();
        $this->assertSame($this->ldap, $service);
    }

    public function testCreateShibService()
    {
        $this->config->shouldReceive('get')->once()->with('authentication_type')->andReturn('shibboleth');

        $service = $this->obj->createAuthenticationService();
        $this->assertSame($this->shib, $service);
    }

    public function testCreateUnknownService()
    {
        $this->expectException(\Exception::class);
        $this->config->shouldReceive('get')->once()->with('authentication_type')->andReturn('nothing');

        $this->obj->createAuthenticationService();
    }
}
