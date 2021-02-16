<?php declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

final class Version20190201190000 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql("UPDATE term SET title=CONCAT('(No Title Provided)_', term_id) WHERE title='' OR title IS NULL");
        $this->addSql("UPDATE vocabulary SET title=CONCAT('(No Title Provided)_', vocabulary_id) WHERE title='' OR title IS NULL");
        $this->addSql('ALTER TABLE term CHANGE title title VARCHAR(200) NOT NULL');
        $this->addSql('ALTER TABLE vocabulary CHANGE title title VARCHAR(200) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE term CHANGE title title VARCHAR(200) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE vocabulary CHANGE title title VARCHAR(200) DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
