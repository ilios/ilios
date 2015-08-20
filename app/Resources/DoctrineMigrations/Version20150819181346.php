<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Renames owning_school and primary_school columns to school.
 *
 * @link https://github.com/ilios/ilios/issues/922
 */
class Version20150819181346 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE competency DROP FOREIGN KEY FK_80D53430DDDDCC69');
        $this->addSql('DROP INDEX IDX_80D53430DDDDCC69 ON competency');
        $this->addSql('ALTER TABLE competency CHANGE owning_school_id school_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE competency ADD CONSTRAINT FK_80D53430C32A47EE FOREIGN KEY (school_id) REFERENCES school (school_id)');
        $this->addSql('CREATE INDEX IDX_80D53430C32A47EE ON competency (school_id)');
        $this->addSql('ALTER TABLE session_type DROP FOREIGN KEY FK_4AAF5703DDDDCC69');
        $this->addSql('DROP INDEX owning_school_id ON session_type');
        $this->addSql('ALTER TABLE session_type CHANGE owning_school_id school_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE session_type ADD CONSTRAINT FK_4AAF5703C32A47EE FOREIGN KEY (school_id) REFERENCES school (school_id)');
        $this->addSql('CREATE INDEX school_id ON session_type (school_id)');
        $this->addSql('ALTER TABLE program DROP FOREIGN KEY FK_92ED7784DDDDCC69');
        $this->addSql('DROP INDEX IDX_92ED7784DDDDCC69 ON program');
        $this->addSql('ALTER TABLE program CHANGE owning_school_id school_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE program ADD CONSTRAINT FK_92ED7784C32A47EE FOREIGN KEY (school_id) REFERENCES school (school_id)');
        $this->addSql('CREATE INDEX IDX_92ED7784C32A47EE ON program (school_id)');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB9DDDDCC69');
        $this->addSql('DROP INDEX IDX_169E6FB9DDDDCC69 ON course');
        $this->addSql('ALTER TABLE course CHANGE owning_school_id school_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9C32A47EE FOREIGN KEY (school_id) REFERENCES school (school_id)');
        $this->addSql('CREATE INDEX IDX_169E6FB9C32A47EE ON course (school_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499B1FA09');
        $this->addSql('DROP INDEX fkey_user_primary_school ON user');
        $this->addSql('ALTER TABLE user CHANGE primary_school_id school_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C32A47EE FOREIGN KEY (school_id) REFERENCES school (school_id)');
        $this->addSql('CREATE INDEX fkey_user_school ON user (school_id)');
        $this->addSql('ALTER TABLE discipline DROP FOREIGN KEY FK_75BEEE3FDDDDCC69');
        $this->addSql('DROP INDEX IDX_75BEEE3FDDDDCC69 ON discipline');
        $this->addSql('ALTER TABLE discipline CHANGE owning_school_id school_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE discipline ADD CONSTRAINT FK_75BEEE3FC32A47EE FOREIGN KEY (school_id) REFERENCES school (school_id)');
        $this->addSql('CREATE INDEX IDX_75BEEE3FC32A47EE ON discipline (school_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE competency DROP FOREIGN KEY FK_80D53430C32A47EE');
        $this->addSql('DROP INDEX IDX_80D53430C32A47EE ON competency');
        $this->addSql('ALTER TABLE competency CHANGE school_id owning_school_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE competency ADD CONSTRAINT FK_80D53430DDDDCC69 FOREIGN KEY (owning_school_id) REFERENCES school (school_id)');
        $this->addSql('CREATE INDEX IDX_80D53430DDDDCC69 ON competency (owning_school_id)');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB9C32A47EE');
        $this->addSql('DROP INDEX IDX_169E6FB9C32A47EE ON course');
        $this->addSql('ALTER TABLE course CHANGE school_id owning_school_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9DDDDCC69 FOREIGN KEY (owning_school_id) REFERENCES school (school_id)');
        $this->addSql('CREATE INDEX IDX_169E6FB9DDDDCC69 ON course (owning_school_id)');
        $this->addSql('ALTER TABLE discipline DROP FOREIGN KEY FK_75BEEE3FC32A47EE');
        $this->addSql('DROP INDEX IDX_75BEEE3FC32A47EE ON discipline');
        $this->addSql('ALTER TABLE discipline CHANGE school_id owning_school_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE discipline ADD CONSTRAINT FK_75BEEE3FDDDDCC69 FOREIGN KEY (owning_school_id) REFERENCES school (school_id)');
        $this->addSql('CREATE INDEX IDX_75BEEE3FDDDDCC69 ON discipline (owning_school_id)');
        $this->addSql('ALTER TABLE program DROP FOREIGN KEY FK_92ED7784C32A47EE');
        $this->addSql('DROP INDEX IDX_92ED7784C32A47EE ON program');
        $this->addSql('ALTER TABLE program CHANGE school_id owning_school_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE program ADD CONSTRAINT FK_92ED7784DDDDCC69 FOREIGN KEY (owning_school_id) REFERENCES school (school_id)');
        $this->addSql('CREATE INDEX IDX_92ED7784DDDDCC69 ON program (owning_school_id)');
        $this->addSql('ALTER TABLE session_type DROP FOREIGN KEY FK_4AAF5703C32A47EE');
        $this->addSql('DROP INDEX school_id ON session_type');
        $this->addSql('ALTER TABLE session_type CHANGE school_id owning_school_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE session_type ADD CONSTRAINT FK_4AAF5703DDDDCC69 FOREIGN KEY (owning_school_id) REFERENCES school (school_id)');
        $this->addSql('CREATE INDEX owning_school_id ON session_type (owning_school_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649C32A47EE');
        $this->addSql('DROP INDEX fkey_user_school ON user');
        $this->addSql('ALTER TABLE user CHANGE school_id primary_school_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499B1FA09 FOREIGN KEY (primary_school_id) REFERENCES school (school_id)');
        $this->addSql('CREATE INDEX fkey_user_primary_school ON user (primary_school_id)');
    }
}
