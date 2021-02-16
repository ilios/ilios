<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add director to programs
 */
final class Version20161014032909 extends MysqlMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE program_director (program_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_9FA52FC53EB8070A (program_id), INDEX IDX_9FA52FC5A76ED395 (user_id), PRIMARY KEY(program_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE program_director ADD CONSTRAINT FK_9FA52FC53EB8070A FOREIGN KEY (program_id) REFERENCES program (program_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE program_director ADD CONSTRAINT FK_9FA52FC5A76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE program_director');
    }
}
