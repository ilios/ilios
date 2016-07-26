<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Make IlmSession the owning side of the session facet relationship
 */
class Version20150826215733 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ilm_session_facet ADD session_id INT DEFAULT NULL');
        $this->addSql(
            'ALTER TABLE ilm_session_facet ADD CONSTRAINT FK_8C070D9613FECDF FOREIGN KEY (session_id) ' .
            'REFERENCES session (session_id) ON DELETE CASCADE'
        );
        $this->addSql(
            'UPDATE ilm_session_facet f SET session_id = ' .
            '(SELECT session_id FROM session s WHERE s.ilm_session_facet_id = f.ilm_session_facet_id)'
        );
        $this->addSql('DELETE FROM ilm_session_facet WHERE session_id IS NULL');
        $this->addSql('ALTER TABLE ilm_session_facet DROP FOREIGN KEY FK_8C070D9613FECDF');
        $this->addSql('ALTER TABLE ilm_session_facet MODIFY session_id INT NOT NULL');
        $this->addSql(
            'ALTER TABLE ilm_session_facet ADD CONSTRAINT FK_8C070D9613FECDF FOREIGN KEY (session_id) ' .
            'REFERENCES session (session_id) ON DELETE CASCADE'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8C070D9613FECDF ON ilm_session_facet (session_id)');
        
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4504270C1');
        $this->addSql('DROP INDEX UNIQ_D044D5D4504270C1 ON session');
        $this->addSql('DROP INDEX session_ibfk_3 ON session');
        $this->addSql('ALTER TABLE session DROP ilm_session_facet_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ilm_session_facet DROP FOREIGN KEY FK_8C070D9613FECDF');
        $this->addSql('DROP INDEX UNIQ_8C070D9613FECDF ON ilm_session_facet');
        $this->addSql('ALTER TABLE session ADD ilm_session_facet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4504270C1 FOREIGN KEY (ilm_session_facet_id) REFERENCES ilm_session_facet (ilm_session_facet_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D044D5D4504270C1 ON session (ilm_session_facet_id)');
        $this->addSql('CREATE INDEX session_ibfk_3 ON session (ilm_session_facet_id)');
        $this->addSql(
            'UPDATE session s SET ilm_session_facet_id = ' .
            '(SELECT ilm_session_facet_id FROM ilm_session_facet f WHERE f.session_id = s.session_id)'
        );
        $this->addSql('ALTER TABLE ilm_session_facet DROP session_id');
    }
}
