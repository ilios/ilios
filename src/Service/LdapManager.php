<?php

declare(strict_types=1);

namespace App\Service;

use Exception;
use Symfony\Component\Ldap\Ldap;

/**
 * Manages the LDAP connection. Because the Symfony ldap class is marked as final,
 * we need to wrap it here so that we can mock it in tests. This class is the lowest
 * level in the test tree, it can't be tested itself because of the issue with mocking
 * the final class.
 */
class LdapManager
{
    protected Ldap $ldap;

    /**
     * Constructor
     */
    public function __construct(protected Config $config)
    {
    }

    /**
     * Performs an LDAP search
     */
    public function search(string $filter): array
    {
        $ldapSearchBase = $this->config->get('ldap_directory_search_base');
        $ldapCampusIdProperty = $this->config->get('ldap_directory_campus_id_property');
        $ldapUsernameProperty = $this->config->get('ldap_directory_username_property');
        $ldapDisplayNameProperty = $this->config->get('ldap_directory_display_name_property');
        $ldapPronounsProperty = $this->config->get('ldap_directory_pronouns_property');
        $ldapFirstNameProperty = $this->config->get('ldap_directory_first_name_property') ?? 'givenName';
        $ldapMiddleNameProperty = $this->config->get('ldap_directory_middle_name_property');
        $ldapLastNameProperty = $this->config->get('ldap_directory_last_name_property') ?? 'sn';
        $ldapPreferredFirstNameProperty = $this->config->get('ldap_directory_preferred_first_name_property');
        $ldapPreferredMiddleNameProperty = $this->config->get('ldap_directory_preferred_middle_name_property');
        $ldapPreferredLastNameProperty = $this->config->get('ldap_directory_preferred_last_name_property');

        $rhett = [];
        try {
            $ldap = $this->getConnection();
            $query = $ldap->query($ldapSearchBase, $filter);
            $results = $query->execute();
            $attributes = [
                'mail' => 'email',
                $ldapLastNameProperty => 'lastName',
                $ldapFirstNameProperty => 'firstName',
                'telephoneNumber' => 'telephoneNumber',
                $ldapCampusIdProperty => 'campusId',
                $ldapUsernameProperty => 'username',
                $ldapDisplayNameProperty => 'displayName',
                $ldapPronounsProperty => 'pronouns',
            ];
            if ($ldapPreferredFirstNameProperty) {
                $attributes[$ldapPreferredFirstNameProperty] = 'preferredFirstName';
            }
            if ($ldapMiddleNameProperty) {
                $attributes[$ldapMiddleNameProperty] = 'middleName';
            }
            if ($ldapPreferredMiddleNameProperty) {
                $attributes[$ldapPreferredMiddleNameProperty] = 'preferredMiddleName';
            }
            if ($ldapPreferredLastNameProperty) {
                $attributes[$ldapPreferredLastNameProperty] = 'preferredLastName';
            }

            foreach ($results as $userData) {
                $values = [];
                foreach ($attributes as $ldapKey => $iliosKey) {
                    $value = $userData->hasAttribute($ldapKey) ? $userData->getAttribute($ldapKey)[0] : null;
                    $values[$iliosKey] = $value;
                }
                $rhett[] = $values;
            }
            usort($rhett, function (array $arr1, array $arr2) {
                $firstName1 = $arr1['firstName'] ?? '';
                $lastName1 = $arr1['lastName'] ?? '';
                $firstName2 = $arr2['firstName'] ?? '';
                $lastName2 = $arr2['lastName'] ?? '';
                if ($lastName1 === $lastName2) {
                    return strcmp($firstName1, $firstName2);
                }

                return strcmp($lastName1, $lastName2);
            });
        } catch (Exception $e) {
            throw new Exception("Failed to search external user source: {$e->getMessage()}");
        }

        return $rhett;
    }

    protected function getConnection(): Ldap
    {
        if (!isset($this->ldap)) {
            $ldapUrl = $this->config->get('ldap_directory_url');
            $ldapBindUser = $this->config->get('ldap_directory_user');
            $ldapBindPassword = $this->config->get('ldap_directory_password');
            $this->ldap = Ldap::create('ext_ldap', [
                'connection_string' => $ldapUrl,
            ]);
            $this->ldap->bind($ldapBindUser, $ldapBindPassword);
        }
        return $this->ldap;
    }
}
