<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\Yaml\Yaml;

/**
 * Copy configuration from parameters.yml into the DB
 */
class Version20170716000000 extends AbstractMigration
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
            $names = array_map(function(array $arr) {
                return $arr['name'];
            }, $rows);
            $names = join(', ', $names);
            throw new \Exception("Cannot copy parameters in the DB it already contains values for [${names}]");
        }
        unset($rows);

        $parametersPath = realpath(__DIR__ . '/../../config/parameters.yml');
        if (is_readable($parametersPath) && is_writable($parametersPath)) {
            $parameters = Yaml::parse(file_get_contents($parametersPath));
            if (array_key_exists('parameters', $parameters)) {
                $symfonyConfigKeys = [
                    'database_driver',
                    'database_host',
                    'database_port',
                    'database_name',
                    'database_user',
                    'database_password',
                    'database_mysql_version',
                    'mailer_transport',
                    'mailer_host',
                    'mailer_user',
                    'mailer_password',
                    'locale',
                    'secret'
                ];

                $itemsToCopy = array_diff_key( $parameters['parameters'], array_flip($symfonyConfigKeys) );
                $copiedKeys = array_keys($itemsToCopy);
                $cleanParameters = $this->cleanup($itemsToCopy);

                foreach ($cleanParameters as $item) {
                    $this->addSql('INSERT INTO application_config (name, value) VALUES (:name, :value)', $item);
                }
                $itemsToKeep = array_diff_key( $parameters['parameters'], array_flip($copiedKeys) );

                $newConfig = [];
                $newConfig['parameters'] = $itemsToKeep;
                $string = Yaml::dump($newConfig);
                file_put_contents($parametersPath, $string);
            }
        } else {
            throw new \Exception("Unable to read and write parameters file at ${parametersPath}");
        }
    }

    /**
     * Cleanup our parameters to remove defaults and null values, and convert booleans to integers
     * @param array $parameters
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
        $mapped = array_map(function ($name, $value) {
            if (is_bool($value)) {
                $value = (integer) $value;
            }
            return [
                'name' => $name,
                'value'  => $value
            ];
        }, array_keys($parameters), $parameters);

        $clean = array_filter($mapped, function($arr) {
            return !is_null($arr['value']);
        });

        return $clean;
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->throwIrreversibleMigrationException('This migration copyied configuration into the DB, copying out again does not really make sense.');
    }
}
