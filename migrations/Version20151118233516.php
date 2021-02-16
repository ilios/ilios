<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Cascade deletes for cohorts and program years
 */
final class Version20151118233516 extends MysqlMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE cohort DROP FOREIGN KEY FK_D3B8C16BCB2B0673');
        $this->addSql('ALTER TABLE cohort ADD CONSTRAINT FK_D3B8C16BCB2B0673 FOREIGN KEY (program_year_id) REFERENCES program_year (program_year_id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE cohort DROP FOREIGN KEY FK_D3B8C16BCB2B0673');
        $this->addSql('ALTER TABLE cohort ADD CONSTRAINT FK_D3B8C16BCB2B0673 FOREIGN KEY (program_year_id) REFERENCES program_year (program_year_id)');
    }
}
