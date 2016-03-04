<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migrate topics data to terms and vocabularies.
 * Then, remove topics from the schema.
 */
class Version20160303215618 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("INSERT INTO vocabulary (title, school_id) (SELECT DISTINCT 'Topics', school_id FROM discipline ORDER BY school_id)");
        $this->addSql("INSERT INTO term (term_id, title, description, vocabulary_id) (SELECT d.discipline_id, d.title, '', v.vocabulary_id FROM discipline d JOIN vocabulary v ON v.school_id = d.school_id ORDER BY d.discipline_id)");
        $this->addSql("INSERT INTO program_year_x_term (program_year_id, term_id) (SELECT program_year_id, discipline_id FROM program_year_x_discipline)");
        $this->addSql("INSERT INTO session_x_term (session_id, term_id) (SELECT session_id, discipline_id FROM session_x_discipline)");
        $this->addSql("INSERT INTO course_x_term (course_id, term_id) (SELECT course_id, discipline_id FROM course_x_discipline)");

        $this->addSql('ALTER TABLE course_x_discipline DROP FOREIGN KEY FK_A52BE633A5522701');
        $this->addSql('ALTER TABLE program_year_x_discipline DROP FOREIGN KEY FK_ED2A7194A5522701');
        $this->addSql('ALTER TABLE session_x_discipline DROP FOREIGN KEY FK_EA7C234FA5522701');
        $this->addSql('DROP TABLE course_x_discipline');
        $this->addSql('DROP TABLE program_year_x_discipline');
        $this->addSql('DROP TABLE session_x_discipline');
        $this->addSql('DROP TABLE discipline');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema)
    {

        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE course_x_discipline (course_id INT NOT NULL, discipline_id INT NOT NULL, INDEX IDX_A52BE633591CC992 (course_id), INDEX IDX_A52BE633A5522701 (discipline_id), PRIMARY KEY(course_id, discipline_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discipline (discipline_id INT AUTO_INCREMENT NOT NULL, school_id INT NOT NULL, title VARCHAR(200) DEFAULT NULL COLLATE utf8_unicode_ci, INDEX IDX_75BEEE3FC32A47EE (school_id), PRIMARY KEY(discipline_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE program_year_x_discipline (program_year_id INT NOT NULL, discipline_id INT NOT NULL, INDEX IDX_ED2A7194A5522701 (discipline_id), INDEX IDX_ED2A7194CB2B0673 (program_year_id), PRIMARY KEY(program_year_id, discipline_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session_x_discipline (session_id INT NOT NULL, discipline_id INT NOT NULL, INDEX IDX_EA7C234FA5522701 (discipline_id), INDEX IDX_EA7C234F613FECDF (session_id), PRIMARY KEY(session_id, discipline_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE course_x_discipline ADD CONSTRAINT FK_A52BE633591CC992 FOREIGN KEY (course_id) REFERENCES course (course_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course_x_discipline ADD CONSTRAINT FK_A52BE633A5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (discipline_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE discipline ADD CONSTRAINT FK_75BEEE3FC32A47EE FOREIGN KEY (school_id) REFERENCES school (school_id)');
        $this->addSql('ALTER TABLE program_year_x_discipline ADD CONSTRAINT FK_ED2A7194A5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (discipline_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE program_year_x_discipline ADD CONSTRAINT FK_ED2A7194CB2B0673 FOREIGN KEY (program_year_id) REFERENCES program_year (program_year_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_x_discipline ADD CONSTRAINT FK_EA7C234F613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_x_discipline ADD CONSTRAINT FK_EA7C234FA5522701 FOREIGN KEY (discipline_id) REFERENCES discipline (discipline_id) ON DELETE CASCADE');

        $this->addSql("INSERT INTO discipline (discipline_id, title, school_id) (SELECT t.term_id, t.title, v.school_id FROM term t JOIN vocabulary v ON t.vocabulary_id =v.vocabulary_id ORDER BY term_id)");
        $this->addSql("INSERT INTO course_x_discipline (course_id, discipline_id) (SELECT course_id, term_id FROM course_x_term)");
        $this->addSql("INSERT INTO session_x_discipline (session_id, discipline_id) (SELECT session_id, term_id FROM session_x_term)");
        $this->addSql("INSERT INTO program_year_x_discipline (program_year_id, discipline_id) (SELECT program_year_id, term_id FROM program_year_x_term)");
        $this->addSql("DELETE FROM term");
        $this->addSql("DELETE FROM vocabulary");
    }
}
