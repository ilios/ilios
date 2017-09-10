<?php

namespace Ilios\CoreBundle\Service;

class Directory
{
    /**
     * @var LdapManager
     */
    protected $ldapManager;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Constructor
     * @param LdapManager $ldapManager
     * @param Config $config
     */
    public function __construct(LdapManager $ldapManager, Config $config)
    {
        $this->ldapManager = $ldapManager;
        $this->config = $config;
    }

    /**
     * Get directory information for a single user
     * @param  string $campusId
     *
     * @return array | false
     */
    public function findByCampusId($campusId)
    {
        $ldapCampusIdProperty = $this->config->get('ldap_directory_campus_id_property');

        $filter = "({$ldapCampusIdProperty}={$campusId})";
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
        $ldapCampusIdProperty = $this->config->get('ldap_directory_campus_id_property');
        $campusIds = array_unique($campusIds);
        $filterTerms = array_map(function ($campusId) use ($ldapCampusIdProperty) {
            return "({$ldapCampusIdProperty}={$campusId})";
        }, $campusIds);

        $users = [];

        //Split into groups of 50 to avoid LDAP query length limits
        foreach (array_chunk($filterTerms, 50) as $terms) {
            $filterTermsString = implode($terms, '');
            $filter = "(|{$filterTermsString})";

            $users = array_merge($users, $this->ldapManager->search($filter));
        }

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
        $ldapCampusIdProperty = $this->config->get('ldap_directory_campus_id_property');
        $filterTerms = array_map(function ($term) use ($ldapCampusIdProperty) {
            $term = ldap_escape($term, null, LDAP_ESCAPE_FILTER);
            return "(|(sn={$term}*)(givenname={$term}*)(mail={$term}*)({$ldapCampusIdProperty}={$term}*))";
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
