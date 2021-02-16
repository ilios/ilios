<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

/**
 * Removes obsolete columns and tables from the schema.
 */
final class Version20210111210836 extends MysqlMigration
{
    public function getDescription() : string
    {
        return 'Removes obsolete columns and tables from the schema.';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE program_year_steward DROP FOREIGN KEY FK_38AC2B7BAE80F5DF');
        $this->addSql('ALTER TABLE course_x_objective DROP FOREIGN KEY FK_3B37B1AD73484933');
        $this->addSql('ALTER TABLE objective DROP FOREIGN KEY FK_B996F101C671CEA1');
        $this->addSql('ALTER TABLE objective_x_mesh DROP FOREIGN KEY FK_936D667473484933');
        $this->addSql('ALTER TABLE objective_x_objective DROP FOREIGN KEY FK_9DC1F2652326141D');
        $this->addSql('ALTER TABLE objective_x_objective DROP FOREIGN KEY FK_9DC1F26573484933');
        $this->addSql('ALTER TABLE program_year_x_objective DROP FOREIGN KEY FK_7A16FDD673484933');
        $this->addSql('ALTER TABLE session_x_objective DROP FOREIGN KEY FK_FA74B40B73484933');
        $this->addSql('DROP TABLE department');
        $this->addSql('DROP TABLE objective');
        $this->addSql('DROP TABLE objective_x_mesh');
        $this->addSql('DROP TABLE objective_x_objective');
        $this->addSql('DROP TABLE program_year_steward');
        $this->addSql('DROP TABLE session_description');
        $this->addSql('DROP INDEX course_objective_uniq ON course_x_objective');
        $this->addSql('DROP INDEX IDX_3B37B1AD73484933 ON course_x_objective');
        $this->addSql('ALTER TABLE course_x_objective DROP objective_id');
        $this->addSql('ALTER TABLE program DROP published_as_tbd, DROP published');
        $this->addSql('ALTER TABLE program_year DROP published_as_tbd, DROP published');
        $this->addSql('DROP INDEX IDX_7A16FDD673484933 ON program_year_x_objective');
        $this->addSql('DROP INDEX program_year_objective_uniq ON program_year_x_objective');
        $this->addSql('ALTER TABLE program_year_x_objective DROP objective_id');
        $this->addSql('DROP INDEX IDX_FA74B40B73484933 ON session_x_objective');
        $this->addSql('DROP INDEX session_objective_uniq ON session_x_objective');
        $this->addSql('ALTER TABLE session_x_objective DROP objective_id');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE department (department_id INT AUTO_INCREMENT NOT NULL, school_id INT NOT NULL, title VARCHAR(90) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, INDEX IDX_CD1DE18AC32A47EE (school_id), PRIMARY KEY(department_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE objective (objective_id INT AUTO_INCREMENT NOT NULL, competency_id INT DEFAULT NULL, ancestor_id INT DEFAULT NULL, title LONGTEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, position INT NOT NULL, active TINYINT(1) NOT NULL, INDEX IDX_B996F101C671CEA1 (ancestor_id), INDEX IDX_B996F101FB9F58C (competency_id), PRIMARY KEY(objective_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE objective_x_mesh (objective_id INT NOT NULL, mesh_descriptor_uid VARCHAR(12) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, INDEX IDX_936D667473484933 (objective_id), INDEX IDX_936D6674CDB3C93B (mesh_descriptor_uid), PRIMARY KEY(objective_id, mesh_descriptor_uid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE objective_x_objective (parent_objective_id INT NOT NULL, objective_id INT NOT NULL, INDEX IDX_9DC1F2652326141D (parent_objective_id), INDEX IDX_9DC1F26573484933 (objective_id), PRIMARY KEY(objective_id, parent_objective_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE program_year_steward (program_year_steward_id INT AUTO_INCREMENT NOT NULL, program_year_id INT NOT NULL, school_id INT NOT NULL, department_id INT DEFAULT NULL, INDEX IDX_38AC2B7BAE80F5DF (department_id), INDEX IDX_38AC2B7BC32A47EE (school_id), INDEX IDX_38AC2B7BCB2B0673 (program_year_id), INDEX IDX_program_year_school (program_year_id, school_id), UNIQUE INDEX program_year_id_school_id_department_id (program_year_id, school_id, department_id), PRIMARY KEY(program_year_steward_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE session_description (description_id INT AUTO_INCREMENT NOT NULL, session_id INT NOT NULL, description LONGTEXT CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`, UNIQUE INDEX UNIQ_91BD5E51613FECDF (session_id), PRIMARY KEY(description_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE department ADD CONSTRAINT FK_CD1DE18AC32A47EE FOREIGN KEY (school_id) REFERENCES school (school_id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE objective ADD CONSTRAINT FK_B996F101C671CEA1 FOREIGN KEY (ancestor_id) REFERENCES objective (objective_id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE objective ADD CONSTRAINT FK_B996F101FB9F58C FOREIGN KEY (competency_id) REFERENCES competency (competency_id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE objective_x_mesh ADD CONSTRAINT FK_936D667473484933 FOREIGN KEY (objective_id) REFERENCES objective (objective_id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE objective_x_mesh ADD CONSTRAINT FK_936D6674CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE objective_x_objective ADD CONSTRAINT FK_9DC1F2652326141D FOREIGN KEY (parent_objective_id) REFERENCES objective (objective_id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE objective_x_objective ADD CONSTRAINT FK_9DC1F26573484933 FOREIGN KEY (objective_id) REFERENCES objective (objective_id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE program_year_steward ADD CONSTRAINT FK_38AC2B7BAE80F5DF FOREIGN KEY (department_id) REFERENCES department (department_id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE program_year_steward ADD CONSTRAINT FK_38AC2B7BC32A47EE FOREIGN KEY (school_id) REFERENCES school (school_id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE program_year_steward ADD CONSTRAINT FK_38AC2B7BCB2B0673 FOREIGN KEY (program_year_id) REFERENCES program_year (program_year_id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_description ADD CONSTRAINT FK_91BD5E51613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course_x_objective ADD objective_id INT NOT NULL');
        $this->addSql('ALTER TABLE course_x_objective ADD CONSTRAINT FK_3B37B1AD73484933 FOREIGN KEY (objective_id) REFERENCES objective (objective_id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX course_objective_uniq ON course_x_objective (course_id, objective_id)');
        $this->addSql('CREATE INDEX IDX_3B37B1AD73484933 ON course_x_objective (objective_id)');
        $this->addSql('ALTER TABLE program ADD published_as_tbd TINYINT(1) NOT NULL, ADD published TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE program_year ADD published_as_tbd TINYINT(1) NOT NULL, ADD published TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE program_year_x_objective ADD objective_id INT NOT NULL');
        $this->addSql('ALTER TABLE program_year_x_objective ADD CONSTRAINT FK_7A16FDD673484933 FOREIGN KEY (objective_id) REFERENCES objective (objective_id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_7A16FDD673484933 ON program_year_x_objective (objective_id)');
        $this->addSql('CREATE UNIQUE INDEX program_year_objective_uniq ON program_year_x_objective (program_year_id, objective_id)');
        $this->addSql('ALTER TABLE session_x_objective ADD objective_id INT NOT NULL');
        $this->addSql('ALTER TABLE session_x_objective ADD CONSTRAINT FK_FA74B40B73484933 FOREIGN KEY (objective_id) REFERENCES objective (objective_id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_FA74B40B73484933 ON session_x_objective (objective_id)');
        $this->addSql('CREATE UNIQUE INDEX session_objective_uniq ON session_x_objective (session_id, objective_id)');
    }
}
