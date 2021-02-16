<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

/**
 * Adds deleting cascades to x-objective join tables.
 */
final class Version20200814223927 extends MysqlMigration
{
    public function getDescription() : string
    {
        return 'Adds deleting cascades to x-objective join tables.';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE session_objective_x_course_objective DROP FOREIGN KEY FK_5EB8C49DBDD5F4B2');
        $this->addSql('ALTER TABLE session_objective_x_course_objective ADD CONSTRAINT FK_5EB8C49DBDD5F4B2 FOREIGN KEY (session_objective_id) REFERENCES session_x_objective (session_objective_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_objective_x_course_objective DROP FOREIGN KEY FK_5EB8C49DF28231CE');
        $this->addSql('ALTER TABLE session_objective_x_course_objective ADD CONSTRAINT FK_5EB8C49DF28231CE FOREIGN KEY (course_objective_id) REFERENCES course_x_objective (course_objective_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course_objective_x_program_year_objective DROP FOREIGN KEY FK_CB20F416F28231CE');
        $this->addSql('ALTER TABLE course_objective_x_program_year_objective ADD CONSTRAINT FK_CB20F416F28231CE FOREIGN KEY (course_objective_id) REFERENCES course_x_objective (course_objective_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course_objective_x_program_year_objective DROP FOREIGN KEY FK_CB20F416BA83A669');
        $this->addSql('ALTER TABLE course_objective_x_program_year_objective ADD CONSTRAINT FK_CB20F416BA83A669 FOREIGN KEY (program_year_objective_id) REFERENCES program_year_x_objective (program_year_objective_id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE course_objective_x_program_year_objective DROP FOREIGN KEY FK_CB20F416F28231CE');
        $this->addSql('ALTER TABLE course_objective_x_program_year_objective ADD CONSTRAINT FK_CB20F416F28231CE FOREIGN KEY (course_objective_id) REFERENCES course_x_objective (course_objective_id)');
        $this->addSql('ALTER TABLE course_objective_x_program_year_objective DROP FOREIGN KEY FK_CB20F416BA83A669');
        $this->addSql('ALTER TABLE course_objective_x_program_year_objective ADD CONSTRAINT FK_CB20F416BA83A669 FOREIGN KEY (program_year_objective_id) REFERENCES program_year_x_objective (program_year_objective_id)');
        $this->addSql('ALTER TABLE session_objective_x_course_objective DROP FOREIGN KEY FK_5EB8C49DBDD5F4B2');
        $this->addSql('ALTER TABLE session_objective_x_course_objective ADD CONSTRAINT FK_5EB8C49DBDD5F4B2 FOREIGN KEY (session_objective_id) REFERENCES session_x_objective (session_objective_id)');
        $this->addSql('ALTER TABLE session_objective_x_course_objective DROP FOREIGN KEY FK_5EB8C49DF28231CE');
        $this->addSql('ALTER TABLE session_objective_x_course_objective ADD CONSTRAINT FK_5EB8C49DF28231CE FOREIGN KEY (course_objective_id) REFERENCES course_x_objective (course_objective_id)');
    }
}
