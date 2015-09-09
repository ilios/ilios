<?php

namespace Ilios\CoreBundle\Service;

use Ilios\CoreBundle\Service\LdapManager;

class Directory
{
    /**
     * @var LdapManager
     */
    protected $ldapManager;

    /**
     * @var string
     */
    protected $ldapCampusIdProperty;

    /**
     * Constructor
     * @param LdapManager   $ldapManager
     * @param string        $ldapCampusIdProperty  injected from configuration
     */
    public function __construct(LdapManager $ldapManager, $ldapCampusIdProperty)
    {
        $this->ldapManager = $ldapManager;
        $this->ldapCampusIdProperty = $ldapCampusIdProperty;
    }
    
    /**
     * Get directory information for a single user
     * @param  string $campusId
     *
     * @return array | false
     */
    public function findUserByCampusId($campusId)
    {
        $filter = "({$this->ldapCampusIdProperty}={$campusId})";
        $users = $this->ldapManager->search($filter);
        if (count($users)) {
            return $users[0];
        }
        
        return false;
    }
}
