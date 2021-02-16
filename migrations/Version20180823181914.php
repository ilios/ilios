<?php declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

/**
 * Add pre/post-requisites to session
 */
final class Version20180823181914 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE session ADD postrequisite_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D42710C13B FOREIGN KEY (postrequisite_id) REFERENCES session (session_id)');
        $this->addSql('CREATE INDEX IDX_D044D5D42710C13B ON session (postrequisite_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D42710C13B');
        $this->addSql('DROP INDEX IDX_D044D5D42710C13B ON session');
        $this->addSql('ALTER TABLE session DROP postrequisite_id');
    }
}
