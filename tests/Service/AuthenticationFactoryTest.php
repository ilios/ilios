<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\CasAuthentication;
use App\Service\FormAuthentication;
use App\Service\LdapAuthentication;
use App\Service\ShibbolethAuthentication;
use App\Service\Config;
use App\Tests\TestCase;
use Exception;
use Mockery as m;
use App\Service\AuthenticationFactory;

final class AuthenticationFactoryTest extends TestCase
{
    protected m\MockInterface $config;
    protected m\MockInterface $cas;
    protected m\MockInterface $form;
    protected m\MockInterface $ldap;
    protected m\MockInterface $shib;
    protected AuthenticationFactory $obj;

    public function setUp(): void
    {
        parent::setUp();
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
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->obj);
        unset($this->config);
        unset($this->cas);
        unset($this->form);
        unset($this->ldap);
        unset($this->shib);
    }

    public function testCreateCasService(): void
    {
        $this->config->shouldReceive('get')->once()->with('authentication_type')->andReturn('cas');

        $service = $this->obj->createAuthenticationService();
        $this->assertSame($this->cas, $service);
    }

    public function testCreateFormService(): void
    {
        $this->config->shouldReceive('get')->once()->with('authentication_type')->andReturn('form');

        $service = $this->obj->createAuthenticationService();
        $this->assertSame($this->form, $service);
    }

    public function testCreateLdapService(): void
    {
        $this->config->shouldReceive('get')->once()->with('authentication_type')->andReturn('ldap');

        $service = $this->obj->createAuthenticationService();
        $this->assertSame($this->ldap, $service);
    }

    public function testCreateShibService(): void
    {
        $this->config->shouldReceive('get')->once()->with('authentication_type')->andReturn('shibboleth');

        $service = $this->obj->createAuthenticationService();
        $this->assertSame($this->shib, $service);
    }

    public function testCreateUnknownService(): void
    {
        $this->expectException(Exception::class);
        $this->config->shouldReceive('get')->once()->with('authentication_type')->andReturn('nothing');

        $this->obj->createAuthenticationService();
    }
}
