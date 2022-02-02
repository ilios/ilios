<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Changes collation on join table to realign it with rest of schema.
 */
final class Version20220202010454 extends MysqlMigration
{
    public function getDescription(): string
    {
        return 'Changes collation on join table to realign it with rest of schema.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE mesh_concept_x_term COLLATE 'utf8_unicode_ci'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE mesh_concept_x_term COLLATE 'utf8_general_ci'");
    }
}
