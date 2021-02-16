<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add directors relationship to schools
 */
final class Version20161012025413 extends MysqlMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE school_director (school_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_E0D6E7B4C32A47EE (school_id), INDEX IDX_E0D6E7B4A76ED395 (user_id), PRIMARY KEY(school_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE school_director ADD CONSTRAINT FK_E0D6E7B4C32A47EE FOREIGN KEY (school_id) REFERENCES school (school_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE school_director ADD CONSTRAINT FK_E0D6E7B4A76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE school_director');
    }
}
