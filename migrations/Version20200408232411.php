<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

/**
 * Adds position column and join table to terms to objectives join tables.
 * Class Version20200408232411
 * @package Ilios\Migrations
 */
final class Version20200408232411 extends MysqlMigration
{
    public function getDescription() : string
    {
        return 'Adds position column and join table to terms to objectives join tables.';
    }

    public function up(Schema $schema) : void
    {
        // session objectives
        // create new join tables
        $this->addSql('CREATE TABLE session_objective (session_objective_id INT AUTO_INCREMENT NOT NULL, session_id INT DEFAULT NULL, objective_id INT NOT NULL, position INT NOT NULL, INDEX IDX_FA74B40B73484933 (objective_id), INDEX IDX_FA74B40B613FECDF (session_id), UNIQUE INDEX session_objective_uniq (session_id, objective_id), PRIMARY KEY(session_objective_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session_objective_x_term (session_objective_id INT NOT NULL, term_id INT NOT NULL, INDEX IDX_F75D1C52BDD5F4B2 (session_objective_id), INDEX IDX_F75D1C52E2C35FC (term_id), PRIMARY KEY(session_objective_id, term_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE session_objective ADD CONSTRAINT FK_FA74B40B613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_objective ADD CONSTRAINT FK_FA74B40B73484933 FOREIGN KEY (objective_id) REFERENCES objective (objective_id)');
        $this->addSql('ALTER TABLE session_objective_x_term ADD CONSTRAINT FK_F75D1C52BDD5F4B2 FOREIGN KEY (session_objective_id) REFERENCES session_objective (session_objective_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_objective_x_term ADD CONSTRAINT FK_F75D1C52E2C35FC FOREIGN KEY (term_id) REFERENCES term (term_id) ON DELETE CASCADE');
        // fill new join table with data from old table while adding position values from session table
        $this->addSql('INSERT INTO session_objective (`session_id`, `objective_id`, `position`) (SELECT sxo.session_id, sxo.objective_id, o.position FROM session_x_objective sxo JOIN objective o ON sxo.objective_id = o.objective_id)');
        // drop old join table
        $this->addSql('DROP TABLE session_x_objective');
        // rename new join table to the old one's name
        $this->addSql('ALTER TABLE session_objective RENAME TO session_x_objective;');

        // do the same thing for course objectives
        $this->addSql('CREATE TABLE course_objective (course_objective_id INT AUTO_INCREMENT NOT NULL, course_id INT DEFAULT NULL, objective_id INT NOT NULL, position INT NOT NULL, INDEX IDX_3B37B1AD591CC992 (course_id), INDEX IDX_3B37B1AD73484933 (objective_id), UNIQUE INDEX course_objective_uniq (course_id, objective_id), PRIMARY KEY(course_objective_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE course_objective_x_term (course_objective_id INT NOT NULL, term_id INT NOT NULL, INDEX IDX_5249C04FF28231CE (course_objective_id), INDEX IDX_5249C04FE2C35FC (term_id), PRIMARY KEY(course_objective_id, term_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE course_objective ADD CONSTRAINT FK_3B37B1AD591CC992 FOREIGN KEY (course_id) REFERENCES course (course_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course_objective ADD CONSTRAINT FK_3B37B1AD73484933 FOREIGN KEY (objective_id) REFERENCES objective (objective_id)');
        $this->addSql('ALTER TABLE course_objective_x_term ADD CONSTRAINT FK_5249C04FF28231CE FOREIGN KEY (course_objective_id) REFERENCES course_objective (course_objective_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course_objective_x_term ADD CONSTRAINT FK_5249C04FE2C35FC FOREIGN KEY (term_id) REFERENCES term (term_id) ON DELETE CASCADE');
        $this->addSql('INSERT INTO course_objective (`course_id`, `objective_id`, `position`) (SELECT cxo.course_id, cxo.objective_id, o.position FROM course_x_objective cxo JOIN objective o ON cxo.objective_id = o.objective_id)');
        $this->addSql('DROP TABLE course_x_objective');
        $this->addSql('ALTER TABLE course_objective RENAME TO course_x_objective;');

        // do the same thing for program year objectives
        $this->addSql('CREATE TABLE program_year_objective (program_year_objective_id INT AUTO_INCREMENT NOT NULL, program_year_id INT DEFAULT NULL, objective_id INT NOT NULL, position INT NOT NULL, INDEX IDX_7A16FDD6CB2B0673 (program_year_id), INDEX IDX_7A16FDD673484933 (objective_id), UNIQUE INDEX program_year_objective_uniq (program_year_id, objective_id), PRIMARY KEY(program_year_objective_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE program_year_objective_x_term (program_year_objective_id INT NOT NULL, term_id INT NOT NULL, INDEX IDX_1BB5B765BA83A669 (program_year_objective_id), INDEX IDX_1BB5B765E2C35FC (term_id), PRIMARY KEY(program_year_objective_id, term_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE program_year_objective ADD CONSTRAINT FK_7A16FDD6CB2B0673 FOREIGN KEY (program_year_id) REFERENCES program_year (program_year_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE program_year_objective ADD CONSTRAINT FK_7A16FDD673484933 FOREIGN KEY (objective_id) REFERENCES objective (objective_id)');
        $this->addSql('ALTER TABLE program_year_objective_x_term ADD CONSTRAINT FK_1BB5B765BA83A669 FOREIGN KEY (program_year_objective_id) REFERENCES program_year_objective (program_year_objective_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE program_year_objective_x_term ADD CONSTRAINT FK_1BB5B765E2C35FC FOREIGN KEY (term_id) REFERENCES term (term_id) ON DELETE CASCADE');
        $this->addSql('INSERT INTO program_year_objective (`program_year_id`, `objective_id`, `position`) (SELECT pxo.program_year_id, pxo.objective_id, o.position FROM program_year_x_objective pxo JOIN objective o ON pxo.objective_id = o.objective_id)');
        $this->addSql('DROP TABLE program_year_x_objective');
        $this->addSql('ALTER TABLE program_year_objective RENAME TO program_year_x_objective');
    }

    public function down(Schema $schema) : void
    {
        $this->throwIrreversibleMigrationException();
    }
}
