<?php

namespace Ilios\AuthenticationBundle\Service;

use Ilios\CoreBundle\Service\Config;

class AuthenticationFactory
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var CasAuthentication
     */
    protected $casAuthentication;

    /**
     * @var LdapAuthentication
     */
    protected $ldapAuthentication;

    /**
     * @var FormAuthentication
     */
    protected $formAuthentication;

    /**
     * @var ShibbolethAuthentication
     */
    protected $shibbolethAuthentication;
    
    public function __construct(
        Config $config,
        CasAuthentication $casAuthentication,
        FormAuthentication $formAuthentication,
        LdapAuthentication $ldapAuthentication,
        ShibbolethAuthentication $shibbolethAuthentication
    ) {
        $this->config = $config;
        $this->casAuthentication = $casAuthentication;
        $this->ldapAuthentication = $ldapAuthentication;
        $this->formAuthentication = $formAuthentication;
        $this->shibbolethAuthentication = $shibbolethAuthentication;
    }

    /**
     * Create the correct service for authentication
     * @return CasAuthentication|FormAuthentication|LdapAuthentication|ShibbolethAuthentication
     * @throws \Exception
     */
    public function createAuthenticationService()
    {
        $authenticationType = $this->config->get('authentication_type');
        switch ($authenticationType) {
            case 'form':
                return $this->formAuthentication;
                break;
            case 'shibboleth':
                return $this->shibbolethAuthentication;
                break;
            case 'ldap':
                return $this->ldapAuthentication;
                break;
            case 'cas':
                return $this->casAuthentication;
                break;
        }

        throw new \Exception("{$authenticationType} is not a valid ilios authenticator");
    }
}
