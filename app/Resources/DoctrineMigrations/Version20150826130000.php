<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migrate MeSH terms into a usable form
 */
class Version20150826130000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $sql = 'SELECT x.mesh_concept_uid, t.mesh_term_id ' .
            'FROM mesh_concept_x_term x JOIN mesh_term t ON ' .
            't.mesh_term_uid = x.mesh_term_uid';
        $rows = $this->connection->executeQuery($sql)->fetchAll();
        if (count($rows)) {
            $insertSql = 'INSERT INTO mesh_concept_x_term (mesh_term_id, mesh_concept_uid) VALUES ';
            $values = array_map(function ($arr) {
                return '("' . $arr['mesh_term_id'] . '","' . $arr['mesh_concept_uid'] . '")';
            }, $rows);
            unset($rows);
            $insertSql .= implode($values, ',');
            unset($values);
        }
        
        $this->addSql('DROP INDEX IDX_100AC50F17293A95 ON mesh_concept_x_term');
        $this->addSql('TRUNCATE mesh_concept_x_term');
        $this->addSql('ALTER TABLE mesh_concept_x_term DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE mesh_concept_x_term ADD mesh_term_id INT NOT NULL');
        $this->addSql('ALTER TABLE mesh_concept_x_term DROP mesh_term_uid');
        $this->addSql(
            'ALTER TABLE mesh_concept_x_term ADD CONSTRAINT FK_100AC50F928873C FOREIGN KEY (mesh_term_id) ' .
            'REFERENCES mesh_term (mesh_term_id)'
        );
        $this->addSql(
            'ALTER TABLE mesh_concept_x_term ADD CONSTRAINT FK_100AC50FE34D9FF5 FOREIGN KEY (mesh_concept_uid) ' .
            'REFERENCES mesh_concept (mesh_concept_uid)'
        );
        $this->addSql('CREATE INDEX IDX_100AC50F928873C ON mesh_concept_x_term (mesh_term_id)');
        $this->addSql('ALTER TABLE mesh_concept_x_term ADD PRIMARY KEY (mesh_term_id, mesh_concept_uid)');
        if (isset($insertSql)) {
            $this->addSql($insertSql);
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() != 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
        $sql = 'SELECT x.mesh_concept_uid, t.mesh_term_uid ' .
            'FROM mesh_concept_x_term x JOIN mesh_term t ON ' .
            't.mesh_term_id = x.mesh_term_id';
        $rows = $this->connection->executeQuery($sql)->fetchAll();
        if (count($rows)) {
            $insertSql = 'INSERT INTO mesh_concept_x_term (mesh_term_uid, mesh_concept_uid) VALUES ';
            $values = array_map(function ($arr) {
                return '("' . $arr['mesh_term_uid'] . '","' . $arr['mesh_concept_uid'] . '")';
            }, $rows);
            $values = array_unique($values);
            unset($rows);
            $insertSql .= implode($values, ',');
            unset($values);
        }
        
        $this->addSql('TRUNCATE mesh_concept_x_term');
        $this->addSql('ALTER TABLE mesh_concept_x_term DROP FOREIGN KEY FK_100AC50F928873C');
        $this->addSql('ALTER TABLE mesh_concept_x_term DROP FOREIGN KEY FK_100AC50FE34D9FF5');
        $this->addSql('DROP INDEX IDX_100AC50F928873C ON mesh_concept_x_term');
        
        $this->addSql('ALTER TABLE mesh_concept_x_term DROP PRIMARY KEY');
        $this->addSql(
            'ALTER TABLE mesh_concept_x_term ' .
            'ADD mesh_term_uid VARCHAR(9) NOT NULL COLLATE utf8_unicode_ci, ' .
            'DROP mesh_term_id'
        );
        $this->addSql('CREATE INDEX IDX_100AC50F17293A95 ON mesh_concept_x_term (mesh_term_uid)');
        $this->addSql('ALTER TABLE mesh_concept_x_term ADD PRIMARY KEY (mesh_concept_uid, mesh_term_uid)');
        if (isset($insertSql)) {
            $this->addSql($insertSql);
        }
    }
}
