<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add active to Terms, Competencies, and Vocabularies
 */
final class Version20160715212648 extends MysqlMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE term ADD active TINYINT(1) NOT NULL');
        $this->addSql('UPDATE term set active=true');

        $this->addSql('ALTER TABLE competency ADD active TINYINT(1) NOT NULL');
        $this->addSql('UPDATE competency set active=true');

        $this->addSql('ALTER TABLE vocabulary ADD active TINYINT(1) NOT NULL');
        $this->addSql('UPDATE vocabulary set active=true');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE term DROP active');
        $this->addSql('ALTER TABLE competency DROP active');
        $this->addSql('ALTER TABLE vocabulary DROP active');
    }
}
