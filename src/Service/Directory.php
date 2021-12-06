<?php

declare(strict_types=1);

namespace App\Service;

class Directory
{
    /**
     * Constructor
     */
    public function __construct(protected LdapManager $ldapManager, protected Config $config)
    {
    }

    /**
     * Get directory information for a single user
     * @param  string $campusId
     */
    public function findByCampusId($campusId): array
    {
        $ldapCampusIdProperty = $this->config->get('ldap_directory_campus_id_property');

        $filter = "({$ldapCampusIdProperty}={$campusId})";
        $users = $this->ldapManager->search($filter);
        if ($users !== []) {
            return $users[0];
        }

        return false;
    }

    /**
     * Get directory information for a list of users
     */
    public function findByCampusIds(array $campusIds): array
    {
        $ldapCampusIdProperty = $this->config->get('ldap_directory_campus_id_property');
        $campusIds = array_unique($campusIds);
        $filterTerms = array_map(fn($campusId) => "({$ldapCampusIdProperty}={$campusId})", $campusIds);

        $users = [];

        //Split into groups of 50 to avoid LDAP query length limits
        foreach (array_chunk($filterTerms, 50) as $terms) {
            $filterTermsString = implode('', $terms);
            $filter = "(|{$filterTermsString})";

            $users = array_merge($users, $this->ldapManager->search($filter));
        }

        if ($users !== []) {
            return $users;
        }

        return false;
    }

    /**
     * Find everyone in the directory matching these terms
     */
    public function find(array $searchTerms): array
    {
        $ldapCampusIdProperty = $this->config->get('ldap_directory_campus_id_property');
        $filterTerms = array_map(function ($term) use ($ldapCampusIdProperty) {
            $term = ldap_escape($term, "", LDAP_ESCAPE_FILTER);
            return "(|(sn={$term}*)(givenname={$term}*)(mail={$term}*)({$ldapCampusIdProperty}={$term}*))";
        }, $searchTerms);
        $filterTermsString = implode('', $filterTerms);
        $filter = "(&{$filterTermsString})";
        $users = $this->ldapManager->search($filter);

        if ($users !== []) {
            return $users;
        }

        return false;
    }

    /**
     * Find all users matching LDAP filter
     * @param  string $filter
     */
    public function findByLdapFilter($filter): array
    {
        $users = $this->ldapManager->search($filter);
        if ($users !== []) {
            return $users;
        }

        return false;
    }
}
