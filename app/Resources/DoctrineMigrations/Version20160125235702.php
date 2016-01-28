<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Creates vocabulary, term and join-tables for terms.
 */
class Version20160125235702 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE course_x_term (course_id INT NOT NULL, term_id INT NOT NULL, INDEX IDX_C6838FC9591CC992 (course_id), INDEX IDX_C6838FC9E2C35FC (term_id), PRIMARY KEY(course_id, term_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE program_year_x_term (program_year_id INT NOT NULL, term_id INT NOT NULL, INDEX IDX_BCB52AB5CB2B0673 (program_year_id), INDEX IDX_BCB52AB5E2C35FC (term_id), PRIMARY KEY(program_year_id, term_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE term (term_id INT AUTO_INCREMENT NOT NULL, parent_term_id INT DEFAULT NULL, vocabulary_id INT NOT NULL, description LONGTEXT DEFAULT NULL, title VARCHAR(200) DEFAULT NULL, INDEX IDX_A50FE78D7C6441BA (parent_term_id), INDEX IDX_A50FE78DAD0E05F6 (vocabulary_id), UNIQUE INDEX unique_term_title (vocabulary_id, title, parent_term_id), PRIMARY KEY(term_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session_x_term (session_id INT NOT NULL, term_id INT NOT NULL, INDEX IDX_7D044DD613FECDF (session_id), INDEX IDX_7D044DDE2C35FC (term_id), PRIMARY KEY(session_id, term_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vocabulary (vocabulary_id INT AUTO_INCREMENT NOT NULL, school_id INT NOT NULL, title VARCHAR(200) DEFAULT NULL, INDEX IDX_9099C97BC32A47EE (school_id), UNIQUE INDEX unique_vocabulary_title (school_id, title), PRIMARY KEY(vocabulary_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE course_x_term ADD CONSTRAINT FK_C6838FC9591CC992 FOREIGN KEY (course_id) REFERENCES course (course_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course_x_term ADD CONSTRAINT FK_C6838FC9E2C35FC FOREIGN KEY (term_id) REFERENCES term (term_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE program_year_x_term ADD CONSTRAINT FK_BCB52AB5CB2B0673 FOREIGN KEY (program_year_id) REFERENCES program_year (program_year_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE program_year_x_term ADD CONSTRAINT FK_BCB52AB5E2C35FC FOREIGN KEY (term_id) REFERENCES term (term_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE term ADD CONSTRAINT FK_A50FE78D7C6441BA FOREIGN KEY (parent_term_id) REFERENCES term (term_id)');
        $this->addSql('ALTER TABLE term ADD CONSTRAINT FK_A50FE78DAD0E05F6 FOREIGN KEY (vocabulary_id) REFERENCES vocabulary (vocabulary_id)');
        $this->addSql('ALTER TABLE session_x_term ADD CONSTRAINT FK_7D044DD613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_x_term ADD CONSTRAINT FK_7D044DDE2C35FC FOREIGN KEY (term_id) REFERENCES term (term_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE vocabulary ADD CONSTRAINT FK_9099C97BC32A47EE FOREIGN KEY (school_id) REFERENCES school (school_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE course_x_term DROP FOREIGN KEY FK_C6838FC9E2C35FC');
        $this->addSql('ALTER TABLE program_year_x_term DROP FOREIGN KEY FK_BCB52AB5E2C35FC');
        $this->addSql('ALTER TABLE term DROP FOREIGN KEY FK_A50FE78D7C6441BA');
        $this->addSql('ALTER TABLE session_x_term DROP FOREIGN KEY FK_7D044DDE2C35FC');
        $this->addSql('ALTER TABLE term DROP FOREIGN KEY FK_A50FE78DAD0E05F6');
        $this->addSql('DROP TABLE course_x_term');
        $this->addSql('DROP TABLE program_year_x_term');
        $this->addSql('DROP TABLE term');
        $this->addSql('DROP TABLE session_x_term');
        $this->addSql('DROP TABLE vocabulary');
    }
}
