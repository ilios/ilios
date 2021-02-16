<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Move all eppn entries to the username on authentication and drop the eppn column
 */
final class Version20150914052024 extends MysqlMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('UPDATE authentication SET username=eppn WHERE eppn IS NOT NULL');
        $this->addSql('DROP INDEX UNIQ_FEB4C9FDFC7885D4 ON authentication');
        $this->addSql('ALTER TABLE authentication DROP eppn');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE authentication ADD eppn VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FEB4C9FDFC7885D4 ON authentication (eppn)');
        $this->addSql('UPDATE authentication SET eppn=username');
        $this->addSql('UPDATE authentication SET username=null');
    }
}
