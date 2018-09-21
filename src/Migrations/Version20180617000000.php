<?php declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Remove parent/child relationships from Orphaned Objectives
 */
final class Version20180617000000 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $related = 'SELECT objective_id FROM objective WHERE ' .
          'objective_id NOT IN (SELECT objective_id FROM program_year_x_objective) ' .
          'AND objective_id NOT IN (SELECT objective_id FROM course_x_objective) ' .
          'AND objective_id NOT IN (SELECT objective_id FROM session_x_objective) ';

        $this->addSql("DELETE FROM objective_x_objective WHERE objective_id IN (${related})");
        $this->addSql("DELETE FROM objective_x_objective WHERE parent_objective_id IN (${related})");
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    }
}
