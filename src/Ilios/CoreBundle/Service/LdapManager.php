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
     * @var string
     */
    protected $ldapCampusIdProperty;
    
    /**
     * @var string
     */
    protected $ldapUsernameProperty;
    
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
     * @param string $ldapUsernameProperty  injected from configuration
     */
    public function __construct(
        $ldapUrl,
        $ldapBindUser,
        $ldapBindPassword,
        $ldapSearchBase,
        $ldapCampusIdProperty,
        $ldapUsernameProperty
    ) {
        $this->ldapUrl = $ldapUrl;
        $this->ldapBindUser = $ldapBindUser;
        $this->ldapBindPassword = $ldapBindPassword;
        $this->ldapSearchBase = $ldapSearchBase;
        $this->ldapCampusIdProperty = $ldapCampusIdProperty;
        $this->ldapUsernameProperty = $ldapUsernameProperty;

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
        $this->ldap->setOption(Ldap::OPT_NETWORK_TIMEOUT, 10);
        $this->ldap->bind($this->ldapBindUser, $this->ldapBindPassword);

        return $this->ldap;
    }

    /**
     * Performs an LDAP search
     * @param string $filter
     *
     * @return array
     * @throws \Exception
     */
    public function search($filter)
    {
        $rhett = [];
        $attributes = [
            'mail',
            'sn',
            'givenName',
            'telephoneNumber',
            $this->ldapCampusIdProperty,
            $this->ldapUsernameProperty
        ];
        try {
            $ldap = $this->getLdap();
            $results = [];
            $cookie = '';
            do {
                $ldap->pagedResult(1000, false, $cookie);
                $response = $ldap->search($this->ldapSearchBase, $filter, $attributes);
                $arr = $response->getEntries();
                unset($arr['count']);
                $results = array_merge($results, $arr);
                $pagedArray = $response->pagedResultResponse();
                $cookie = !empty($pagedArray['cookie'])?$pagedArray['cookie']:false;
            } while ($cookie);

            if (count($results)) {
                $campusIdKey = strtolower($this->ldapCampusIdProperty);
                $usernameKey = strtolower($this->ldapUsernameProperty);
                $rhett = array_map(function ($userData) use ($campusIdKey, $usernameKey) {
                    $keys = [
                        'givenname',
                        'sn',
                        'mail',
                        'telephonenumber',
                        $campusIdKey,
                        $usernameKey
                    ];
                    $values = [];
                    foreach ($keys as $key) {
                        $value = array_key_exists($key, $userData)?$userData[$key][0]:null;
                        $values[$key] = $value;
                    }
                    return [
                        'firstName' => $values['givenname'],
                        'lastName' => $values['sn'],
                        'email' => $values['mail'],
                        'telephoneNumber' => $values['telephonenumber'],
                        'campusId' => $values[$campusIdKey],
                        'username' => $values[$usernameKey],
                    ];
                }, $results);
                
                usort($rhett, function (array $arr1, array $arr2) {
                    if ($arr1['lastName'] == $arr2['lastName']) {
                        if ($arr1['firstName'] == $arr2['firstName']) {
                            return 0;
                        }
                        return strcmp($arr1['firstName'], $arr2['firstName']);
                    }
                    
                    return strcmp($arr1['lastName'], $arr2['lastName']);
                });
            }
        } catch (\UserException $e) {
            throw new \Exception("Failed to search external user source: {$e->getMessage()}");
        }
    
        return $rhett;
        
    }
}
