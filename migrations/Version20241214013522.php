<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Drops MeshConcept::registryNumber.
 */
final class Version20241214013522 extends MysqlMigration
{
    public function getDescription(): string
    {
        return 'Drops MeshConcept::registryNumber.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE mesh_concept DROP registry_number');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE mesh_concept ADD registry_number VARCHAR(30) DEFAULT NULL');
    }
}
