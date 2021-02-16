<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Removes the api_key table.
 *
 * @link https://github.com/ilios/ilios/issues/966
 */
final class Version20150820013804 extends MysqlMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('DROP TABLE api_key');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql(
            'CREATE TABLE api_key (user_id INT NOT NULL, api_key VARCHAR(64) NOT NULL COLLATE utf8_unicode_ci, UNIQUE INDEX UNIQ_C912ED9DA76ED395 (user_id), UNIQUE INDEX api_key_api_key (api_key), PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
    }
}
