<?php

namespace Ilios\AuthenticationBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Ilios\AuthenticationBundle\DependencyInjection\IliosAuthenticationExtension;

class IliosAuthenticationExtensionTest extends AbstractExtensionTestCase
{

    protected function getContainerExtensions()
    {
        return array(
            new IliosAuthenticationExtension()
        );
    }

    public function testParametersSet()
    {
        $legacySalt = 'legacy_salt';
        $ldapHost = 'ldap_authentication_host';
        $ldapPort = 'ldap_authentication_port';
        $shibbLoginPath = 'shibboleth_authentication_login_path';
        $shibbLogoutPath = 'shibboleth_authentication_logout_path';
        $shibbUserIdAttribute = 'shibboleth_authentication_user_id_attribute';

        $this->load(array(
            'legacy_salt' => $legacySalt,
            'type' => 'form',
            'ldap_authentication_host' => $ldapHost,
            'ldap_authentication_port' => $ldapPort,
            'shibboleth_authentication_login_path' => $shibbLoginPath,
            'shibboleth_authentication_logout_path' => $shibbLogoutPath,
            'shibboleth_authentication_user_id_attribute' => $shibbUserIdAttribute,
        ));
        $parameters = array(
            'ilios_authentication.legacy_salt' => $legacySalt,
            'ilios_authentication.authenticatorservice' => 'ilios_authentication.form.authentication',
            'ilios_authentication.ldap.host' => $ldapHost,
            'ilios_authentication.ldap.port' => $ldapPort,
            'ilios_authentication.shibboleth.login_path' => $shibbLoginPath,
            'ilios_authentication.shibboleth.logout_path' => $shibbLogoutPath,
            'ilios_authentication.shibboleth.user_id_attribute' => $shibbUserIdAttribute,
        );
        foreach ($parameters as $name => $value) {
            $this->assertContainerBuilderHasParameter($name, $value);
        }
        $services = array(
            'ilios_authentication.jwt.authenticator',
            'ilios_authentication.jwt.add_header',
            'ilios_authentication.jwt.manager',
            'ilios_authentication.form.legacy_encoder',
            'ilios_authentication.form.authentication',
            'ilios_authentication.shibboleth.authentication',
            'ilios_authentication.ldap.authentication',
            'ilios_authentication.authenticator_factory',
            'ilios_authentication.authenticator'
        );
        foreach ($services as $service) {
            $this->assertContainerBuilderHasService($service);
        }
    }

    public function testShibbolethAuthConfig()
    {
        $this->load(array(
            'legacy_salt' => 'salt',
            'type' => 'shibboleth',
            'ldap_authentication_host' => 'host',
            'ldap_authentication_port' => 'port',
            'shibboleth_authentication_login_path' => 'login_path',
            'shibboleth_authentication_logout_path' => 'logout_path',
            'shibboleth_authentication_user_id_attribute' => 'user_id',
        ));
        $parameters = array(
            'ilios_authentication.authenticatorservice' => 'ilios_authentication.shibboleth.authentication',
        );
        foreach ($parameters as $name => $value) {
            $this->assertContainerBuilderHasParameter($name, $value);
        }
    }

    public function testLdapAuthConfig()
    {
        $this->load(array(
            'legacy_salt' => 'salt',
            'type' => 'ldap',
            'ldap_authentication_host' => 'host',
            'ldap_authentication_port' => 'port',
            'shibboleth_authentication_login_path' => 'login_path',
            'shibboleth_authentication_logout_path' => 'logout_path',
            'shibboleth_authentication_user_id_attribute' => 'user_id',
        ));
        $parameters = array(
            'ilios_authentication.authenticatorservice' => 'ilios_authentication.ldap.authentication',
        );
        foreach ($parameters as $name => $value) {
            $this->assertContainerBuilderHasParameter($name, $value);
        }
    }
}
