<?php

declare(strict_types=1);

namespace App\Service;

class AuthenticationFactory
{
    public function __construct(
        protected Config $config,
        protected CasAuthentication $casAuthentication,
        protected FormAuthentication $formAuthentication,
        protected LdapAuthentication $ldapAuthentication,
        protected ShibbolethAuthentication $shibbolethAuthentication
    ) {
    }

    /**
     * Create the correct service for authentication
     * @throws \Exception
     */
    public function createAuthenticationService(): CasAuthentication|FormAuthentication|LdapAuthentication|ShibbolethAuthentication
    {
        $authenticationType = $this->config->get('authentication_type');
        switch ($authenticationType) {
            case 'form':
                return $this->formAuthentication;
            case 'shibboleth':
                return $this->shibbolethAuthentication;
            case 'ldap':
                return $this->ldapAuthentication;
            case 'cas':
                return $this->casAuthentication;
        }

        throw new \Exception("{$authenticationType} is not a valid ilios authenticator");
    }
}
