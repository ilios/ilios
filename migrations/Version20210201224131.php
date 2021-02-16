<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

/**
 * Adds cascading deletes to alert recipients relationship.
 */
final class Version20210201224131 extends MysqlMigration
{
    public function getDescription() : string
    {
        return 'Adds cascading deletes to alert recipients relationship.';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE alert_recipient DROP FOREIGN KEY FK_D97AE69D93035F72');
        $this->addSql('ALTER TABLE alert_recipient DROP FOREIGN KEY FK_D97AE69DC32A47EE');
        $this->addSql('ALTER TABLE alert_recipient ADD CONSTRAINT FK_D97AE69D93035F72 FOREIGN KEY (alert_id) REFERENCES alert (alert_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE alert_recipient ADD CONSTRAINT FK_D97AE69DC32A47EE FOREIGN KEY (school_id) REFERENCES school (school_id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE alert_recipient DROP FOREIGN KEY FK_D97AE69D93035F72');
        $this->addSql('ALTER TABLE alert_recipient DROP FOREIGN KEY FK_D97AE69DC32A47EE');
        $this->addSql('ALTER TABLE alert_recipient ADD CONSTRAINT FK_D97AE69D93035F72 FOREIGN KEY (alert_id) REFERENCES alert (alert_id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE alert_recipient ADD CONSTRAINT FK_D97AE69DC32A47EE FOREIGN KEY (school_id) REFERENCES school (school_id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
