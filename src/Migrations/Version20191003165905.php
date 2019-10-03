<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Joins objectives with terms.
 */
final class Version20191003165905 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE objective_x_term (objective_id INT NOT NULL, term_id INT NOT NULL, INDEX IDX_D70DBBAF73484933 (objective_id), INDEX IDX_D70DBBAFE2C35FC (term_id), PRIMARY KEY(objective_id, term_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE objective_x_term ADD CONSTRAINT FK_D70DBBAF73484933 FOREIGN KEY (objective_id) REFERENCES objective (objective_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE objective_x_term ADD CONSTRAINT FK_D70DBBAFE2C35FC FOREIGN KEY (term_id) REFERENCES term (term_id) ON DELETE CASCADE');
    }


    /**
     * @inheritdoc
     */
    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE objective_x_term');
    }
}
