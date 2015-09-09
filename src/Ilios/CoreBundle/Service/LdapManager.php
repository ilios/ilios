<?php

namespace Ilios\CoreBundle\Service;

use Dreamscapes\Ldap\Core\Ldap;

class LdapManager
{
    /**
     * Reset timeout - if more than this many seconds have passed since the
     * last request we should reset the ldap connection
     * @var int
     */
    const RESET_TIMEOUT = 120;

    /**
     * @var string
     */
    protected $ldapUrl;
    
    /**
     * @var string
     */
    protected $ldapBindUser;
    
    /**
     * @var string
     */
    protected $ldapBindPassword;
    
    /**
     * @var LDAP
     */
    protected $ldap;
    
    /**
     * LDAP connection times tells us when to reset
     * @var int
     */
    protected $connectionLastUsed;

    /**
     * Constructor
     * @param string $ldapUrl               injected from configuration
     * @param string $ldapBindUser          injected from configuration
     * @param string $ldapBindPassword      injected from configuration
     * @param string $ldapSearchBase        injected from configuration
     * @param string $ldapCampusIdProperty  injected from configuration
     */
    public function __construct(
        $ldapUrl,
        $ldapBindUser,
        $ldapBindPassword,
        $ldapSearchBase,
        $ldapCampusIdProperty
    ) {
        $this->ldapUrl = $ldapUrl;
        $this->ldapBindUser = $ldapBindUser;
        $this->ldapBindPassword = $ldapBindPassword;
        $this->ldapSearchBase = $ldapSearchBase;
        $this->ldapCampusIdProperty = $ldapCampusIdProperty;

        $this->ldap = null;
        $this->connectionCreatedAt = null;
    }
    
    /**
     * Destroy the connection
     */
    public function __destruct()
    {
        if ($this->ldap) {
            $this->ldap->close();
        }
    }
    
    /**
     * Get an instance of the LDAP object
     *
     * @return LDAP
     */
    protected function getLdap()
    {
        $now = time();
        if ($this->connectionLastUsed &&
            $this->ldap &&
            $now - $this->connectionLastUsed > self::RESET_TIMEOUT
        ) {
            $this->ldap->close();
            $this->ldap = null;
        }
        $this->connectionLastUsed = $now;
        if (!empty($this->ldap)) {
            return $this->ldap;
        }
        
        $this->ldap = new Ldap($this->ldapUrl);
        $this->ldap->bind($this->ldapBindUser, $this->ldapBindPassword);

        return $this->ldap;
    }

    /**
     * Performs an LDAP search
     * @param string $filter
     * @param string $sortBy
     *
     * @return array
     * @throws Exception
     */
    public function search($filter, $sortBy = null)
    {
        $rhett = [];
        $attributes = [
            'mail',
            'sn',
            'givenName',
            'telephoneNumber',
            $this->ldapCampusIdProperty
        ];
        try {
            $ldap = $this->getLdap();
            $results = $ldap->search($this->ldapSearchBase, $filter, $attributes);
            
            if ($results->countEntries()) {
                if ($sortBy) {
                    $results = $results->sort($sortBy);
                }
                $arr = $results->getEntries();
                unset($arr['count']);
                $campusIdKey = strtolower($this->ldapCampusIdProperty);
                $rhett = array_map(function ($userData) use ($campusIdKey) {
                    $firstName = array_key_exists('givenname', $userData)?$userData['givenname'][0]:null;
                    $lastName = array_key_exists('sn', $userData)?$userData['sn'][0]:null;
                    $email = array_key_exists('mail', $userData)?$userData['mail'][0]:null;
                    $phone = array_key_exists('telephoneNumber', $userData)?$userData['telephoneNumber'][0]:null;
                    $campusId = array_key_exists($campusIdKey, $userData)?$userData[$campusIdKey][0]:null;
                    return [
                        'firstName' => $firstName,
                        'lastName' => $lastName,
                        'email' => $email,
                        'telephoneNumber' => $phone,
                        'campusId' => $campusId,
                    ];
                }, $arr);
            }
            
        } catch (\UserException $e) {
            throw new Exception("Failed to search external user source: {$e->getMessage()}");
        }
    
        return $rhett;
        
    }
}
