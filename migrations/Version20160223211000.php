<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Fix mimetype on link and citation learning materials
 */
final class Version20160223211000 extends MysqlMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql("UPDATE learning_material set mime_type = 'link' WHERE web_link IS NOT NULL");
        $this->addSql("UPDATE learning_material set mime_type = 'citation' WHERE citation IS NOT NULL");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql("UPDATE learning_material set mime_type = NULL WHERE web_link IS NOT NULL");
        $this->addSql("UPDATE learning_material set mime_type = NULL WHERE citation IS NOT NULL");
    }
}
