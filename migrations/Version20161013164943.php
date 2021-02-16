<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add administrator relationship to session
 */
final class Version20161013164943 extends MysqlMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE session_administrator (session_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C2AE285A613FECDF (session_id), INDEX IDX_C2AE285AA76ED395 (user_id), PRIMARY KEY(session_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE session_administrator ADD CONSTRAINT FK_C2AE285A613FECDF FOREIGN KEY (session_id) REFERENCES session (session_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_administrator ADD CONSTRAINT FK_C2AE285AA76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE session_administrator');
    }
}
