<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Adds indeces on text columns of various MeSH tables.
 */
final class Version20220503163022 extends MysqlMigration
{
    public function getDescription(): string
    {
        return 'Adds indeces on text columns of various MeSH tables.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE INDEX idx_mesh_concept_name ON mesh_concept (name)');
        $this->addSql('CREATE INDEX idx_mesh_concept_casn_1_name ON mesh_concept (casn_1_name)');
        $this->addSql('CREATE INDEX idx_mesh_descriptor_annotation ON mesh_descriptor (annotation)');
        $this->addSql('CREATE INDEX idx_mesh_descriptor_name ON mesh_descriptor (name)');
        $this->addSql('CREATE INDEX idx_mesh_previous_indexing_previous_indexing ON mesh_previous_indexing (previous_indexing)');
        $this->addSql('CREATE INDEX idx_mesh_description_name ON mesh_term (name)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_mesh_concept_name ON mesh_concept');
        $this->addSql('DROP INDEX idx_mesh_concept_casn_1_name ON mesh_concept');
        $this->addSql('DROP INDEX idx_mesh_descriptor_annotation ON mesh_descriptor');
        $this->addSql('DROP INDEX idx_mesh_descriptor_name ON mesh_descriptor');
        $this->addSql('DROP INDEX idx_mesh_previous_indexing_previous_indexing ON mesh_previous_indexing');
        $this->addSql('DROP INDEX idx_mesh_description_name ON mesh_term');
    }
}
