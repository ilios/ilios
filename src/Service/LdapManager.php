<?php

declare(strict_types=1);

namespace App\Service;

use Exception;
use Symfony\Component\Ldap\Ldap;

class LdapManager
{
    protected Config $config;
    protected ?Ldap $ldap = null;

    /**
     * Constructor
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
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

        $rhett = [];
        try {
            $ldap = $this->getConnection();
            $query = $ldap->query($ldapSearchBase, $filter);
            $results = $query->execute();
            $attributes = [
                'mail' => 'email',
                'sn' => 'lastName',
                'givenName' => 'firstName',
                'telephoneNumber' => 'telephoneNumber',
                $ldapCampusIdProperty => 'campusId',
                $ldapUsernameProperty => 'username',
                $ldapDisplayNameProperty => 'displayName',
            ];

            foreach ($results as $userData) {
                $values = [];
                foreach ($attributes as $ldapKey => $iliosKey) {
                    $value = $userData->hasAttribute($ldapKey) ? $userData->getAttribute($ldapKey)[0] : null;
                    $values[$iliosKey] = $value;
                }
                $rhett[] = $values;
            }
            usort($rhett, function (array $arr1, array $arr2) {
                if ($arr1['lastName'] == $arr2['lastName']) {
                    if ($arr1['firstName'] == $arr2['firstName']) {
                        return 0;
                    }
                    return strcmp($arr1['firstName'], $arr2['firstName']);
                }

                return strcmp($arr1['lastName'], $arr2['lastName']);
            });
        } catch (Exception $e) {
            throw new Exception("Failed to search external user source: {$e->getMessage()}");
        }

        return $rhett;
    }

    protected function getConnection(): Ldap
    {
        if (! $this->ldap) {
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
