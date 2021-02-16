<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add ancestors to courses and objectives
 */
final class Version20160915043119 extends MysqlMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE course ADD ancestor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9C671CEA1 FOREIGN KEY (ancestor_id) REFERENCES course (course_id)');
        $this->addSql('CREATE INDEX IDX_169E6FB9C671CEA1 ON course (ancestor_id)');
        $this->addSql('ALTER TABLE objective ADD ancestor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE objective ADD CONSTRAINT FK_B996F101C671CEA1 FOREIGN KEY (ancestor_id) REFERENCES objective (objective_id)');
        $this->addSql('CREATE INDEX IDX_B996F101C671CEA1 ON objective (ancestor_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB9C671CEA1');
        $this->addSql('DROP INDEX IDX_169E6FB9C671CEA1 ON course');
        $this->addSql('ALTER TABLE course DROP ancestor_id');
        $this->addSql('ALTER TABLE objective DROP FOREIGN KEY FK_B996F101C671CEA1');
        $this->addSql('DROP INDEX IDX_B996F101C671CEA1 ON objective');
        $this->addSql('ALTER TABLE objective DROP ancestor_id');
    }
}
