<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Converts the ILM Session's "due date" column from "date" to "datetime".
 */
final class Version20160104231711 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE ilm_session_facet CHANGE due_date due_date DATETIME NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE ilm_session_facet CHANGE due_date due_date DATE NOT NULL');
    }
}
