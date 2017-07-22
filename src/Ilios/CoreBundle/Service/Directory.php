<?php

namespace Ilios\CoreBundle\Service;

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
     * @param LdapManager $ldapManager
     * @param Config $config
     */
    public function __construct(LdapManager $ldapManager, Config $config)
    {
        $this->ldapManager = $ldapManager;
        $this->ldapCampusIdProperty = $config->get('ldap_directory_campus_id_property');
    }

    /**
     * Get directory information for a single user
     * @param  string $campusId
     *
     * @return array | false
     */
    public function findByCampusId($campusId)
    {
        $filter = "({$this->ldapCampusIdProperty}={$campusId})";
        $users = $this->ldapManager->search($filter);
        if (count($users)) {
            return $users[0];
        }

        return false;
    }

    /**
     * Get directory information for a list of users
     * @param  array $campusIds
     *
     * @return array | false
     */
    public function findByCampusIds(array $campusIds)
    {
        $campusIds = array_unique($campusIds);
        $filterTerms = array_map(function ($campusId) {
            return "({$this->ldapCampusIdProperty}={$campusId})";
        }, $campusIds);
        $filterTermsString = implode($filterTerms, '');
        $filter = "(|{$filterTermsString})";

        $users = $this->ldapManager->search($filter);
        if (count($users)) {
            return $users;
        }

        return false;
    }

    /**
     * Find everyone in the directory matching these terms
     * @param  array $searchTerms
     *
     * @return array | false
     */
    public function find(array $searchTerms)
    {
        $filterTerms = array_map(function ($term) {
            $term = ldap_escape($term, null, LDAP_ESCAPE_FILTER);
            return "(|(sn={$term}*)(givenname={$term}*)(mail={$term}*)({$this->ldapCampusIdProperty}={$term}*))";
        }, $searchTerms);
        $filterTermsString = implode($filterTerms, '');
        $filter = "(&{$filterTermsString})";
        $users = $this->ldapManager->search($filter);

        if (count($users)) {
            return $users;
        }

        return false;
    }

    /**
     * Find all users matching LDAP filter
     * @param  string $filter
     *
     * @return array | false
     */
    public function findByLdapFilter($filter)
    {
        $users = $this->ldapManager->search($filter);
        if (count($users)) {
            return $users;
        }

        return false;
    }
}
