<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Changes foreign key constraints to allow for cascading deletes.
 */
class Version20160421221726 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE course_director DROP FOREIGN KEY FK_B724BEA6591CC992');
        $this->addSql('ALTER TABLE course_director DROP FOREIGN KEY FK_B724BEA6A76ED395');
        $this->addSql('ALTER TABLE course_director ADD CONSTRAINT FK_B724BEA6591CC992 FOREIGN KEY (course_id) REFERENCES course (course_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course_director ADD CONSTRAINT FK_B724BEA6A76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4591CC992');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4591CC992 FOREIGN KEY (course_id) REFERENCES course (course_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course_learning_material DROP FOREIGN KEY FK_F841D788591CC992');
        $this->addSql('ALTER TABLE course_learning_material ADD CONSTRAINT FK_F841D788591CC992 FOREIGN KEY (course_id) REFERENCES course (course_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course_learning_material_x_mesh DROP FOREIGN KEY FK_476BB36F46C5AD2E');
        $this->addSql('ALTER TABLE course_learning_material_x_mesh DROP FOREIGN KEY FK_476BB36FCDB3C93B');
        $this->addSql('ALTER TABLE course_learning_material_x_mesh ADD CONSTRAINT FK_476BB36F46C5AD2E FOREIGN KEY (course_learning_material_id) REFERENCES course_learning_material (course_learning_material_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course_learning_material_x_mesh ADD CONSTRAINT FK_476BB36FCDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid) ON DELETE CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE course_learning_material_x_mesh DROP FOREIGN KEY FK_476BB36F46C5AD2E');
        $this->addSql('ALTER TABLE course_learning_material_x_mesh DROP FOREIGN KEY FK_476BB36FCDB3C93B');
        $this->addSql('ALTER TABLE course_learning_material_x_mesh ADD CONSTRAINT FK_476BB36F46C5AD2E FOREIGN KEY (course_learning_material_id) REFERENCES course_learning_material (course_learning_material_id)');
        $this->addSql('ALTER TABLE course_learning_material_x_mesh ADD CONSTRAINT FK_476BB36FCDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid)');
        $this->addSql('ALTER TABLE course_learning_material DROP FOREIGN KEY FK_F841D788591CC992');
        $this->addSql('ALTER TABLE course_learning_material ADD CONSTRAINT FK_F841D788591CC992 FOREIGN KEY (course_id) REFERENCES course (course_id)');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4591CC992');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4591CC992 FOREIGN KEY (course_id) REFERENCES course (course_id)');
        $this->addSql('ALTER TABLE course_director DROP FOREIGN KEY FK_B724BEA6591CC992');
        $this->addSql('ALTER TABLE course_director DROP FOREIGN KEY FK_B724BEA6A76ED395');
        $this->addSql('ALTER TABLE course_director ADD CONSTRAINT FK_B724BEA6591CC992 FOREIGN KEY (course_id) REFERENCES course (course_id)');
        $this->addSql('ALTER TABLE course_director ADD CONSTRAINT FK_B724BEA6A76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id)');
    }
}
