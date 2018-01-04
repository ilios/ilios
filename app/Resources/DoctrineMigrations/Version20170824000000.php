<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\Yaml\Yaml;

/**
 * Copy configuration from parameters.yml into the DB and update file with new keys
 */
class Version20170824000000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $sql = 'SELECT name from application_config';
        $rows = $this->connection->executeQuery($sql)->fetchAll();

        if (count($rows)) {
            $names = array_map(function (array $arr) {
                return $arr['name'];
            }, $rows);
            $names = join(', ', $names);
            throw new \Exception("Cannot copy parameters in the DB it already contains values for [${names}]");
        }
        unset($rows);

        $parameters = $this->readParameters();
        $symfonyConfigKeys = [
            'env(ILIOS_DATABASE_DRIVER)',
            'env(ILIOS_DATABASE_HOST)',
            'env(ILIOS_DATABASE_PORT)',
            'env(ILIOS_DATABASE_NAME)',
            'env(ILIOS_DATABASE_USER)',
            'env(ILIOS_DATABASE_PASSWORD)',
            'env(ILIOS_DATABASE_MYSQL_VERSION)',
            'env(ILIOS_MAILER_TRANSPORT)',
            'env(ILIOS_MAILER_HOST)',
            'env(ILIOS_MAILER_USER)',
            'env(ILIOS_MAILER_PASSWORD)',
            'env(ILIOS_LOCALE)',
            'env(ILIOS_SECRET)'
        ];

        $itemsToCopy = array_diff_key($parameters, array_flip($symfonyConfigKeys));
        $copiedKeys = array_keys($itemsToCopy);
        $cleanParameters = $this->cleanup($itemsToCopy);

        foreach ($cleanParameters as $item) {
            $this->addSql('INSERT INTO application_config (name, value) VALUES (:name, :value)', $item);
        }
        $itemsToKeep = array_diff_key($parameters, array_flip($copiedKeys));

        if (!empty($itemsToKeep)) {
            $this->writeParameters($itemsToKeep);
        }
    }

    /**
     * Cleanup our parameters to remove defaults and null values,
     * convert booleans to integers, and modify requirements for forceProtocol
     * which is now a boolean called requireSecureConnection
     * @param array $parameters
     *
     * @return array
     */
    protected function cleanup(array $parameters)
    {
        $defaultsToRemove = [
            'legacy_password_salt' => 'Ilios2 ilios_authentication_internal_auth_salt value',
            'file_system_storage_path' => 'A path on your server\'s file system where Ilios can store files like learning_materials',
            'institution_domain' => 'Internet domain name of this institution, used for curriculum inventory reporting to the AAMC.',
            'supporting_link' => 'Optional \'supporting link\' for the curriculum inventory exports, leave empty to be omitted from reports.',
        ];
        foreach ($defaultsToRemove as $key => $defaultValue) {
            if (array_key_exists($key, $parameters) and $parameters[$key] === $defaultValue) {
                $parameters[$key] = null;
            }
        }
        if (array_key_exists('authentication_type', $parameters)) {
            //clean out defaults for unused authentication types
            if ($parameters['authentication_type'] != 'shibboleth') {
                $parameters['shibboleth_authentication_login_path'] = null;
                $parameters['shibboleth_authentication_logout_path'] = null;
                $parameters['shibboleth_authentication_user_id_attribute'] = null;
            }
            if ($parameters['authentication_type'] != 'cas') {
                $parameters['cas_authentication_version'] = null;
            }
        }
        if (array_key_exists('forceProtocol', $parameters) and $parameters['forceProtocol'] === 'http') {
            $parameters['requireSecureConnection'] = 0;
        } else {
            $parameters['requireSecureConnection'] = 1;
        }
        unset($parameters['forceProtocol']);

        $mapped = array_map(function ($name, $value) {
            if (is_bool($value)) {
                $value = (integer) $value;
            }
            return [
                'name' => $name,
                'value'  => $value
            ];
        }, array_keys($parameters), $parameters);

        $clean = array_filter($mapped, function ($arr) {
            return !is_null($arr['value']);
        });

        return $clean;
    }

    /**
     * Read existing parameters
     * @return array
     * @throws \Exception
     */
    protected function readParameters()
    {
        $parametersPath = realpath(__DIR__ . '/../../config/parameters.yml');
        if (is_readable($parametersPath)) {
            $parameters = Yaml::parse(file_get_contents($parametersPath));
            if (array_key_exists('parameters', $parameters)) {
                return $parameters['parameters'];
            }
        }
        throw new \Exception("Unable to read parameters file at ${parametersPath}");
    }

    /**
     * Write parameters to the file
     * @param $parameters
     * @throws \Exception
     */
    protected function writeParameters($parameters)
    {
        $parametersPath = realpath(__DIR__ . '/../../config/parameters.yml');
        if (!is_writable($parametersPath)) {
            throw new \Exception("Unable to write parameters file at ${parametersPath}");
        }

        $string = Yaml::dump(['parameters' => $parameters]);
        file_put_contents($parametersPath, $string);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->throwIrreversibleMigrationException('This migration copyied configuration into the DB, copying out again does not really make sense.');
    }
}
