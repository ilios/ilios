<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

final class Version20201001230658 extends MysqlMigration
{
    public function getDescription(): string
    {
        return 'Declares cascading deletes on objective/mesh relationships.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE session_objective_x_mesh DROP FOREIGN KEY FK_B33DC189BDD5F4B2');
        $this->addSql('ALTER TABLE session_objective_x_mesh DROP FOREIGN KEY FK_B33DC189CDB3C93B');
        $this->addSql('ALTER TABLE session_objective_x_mesh ADD CONSTRAINT FK_B33DC189BDD5F4B2 FOREIGN KEY (session_objective_id) REFERENCES session_x_objective (session_objective_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE session_objective_x_mesh ADD CONSTRAINT FK_B33DC189CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course_objective_x_mesh DROP FOREIGN KEY FK_16291D94CDB3C93B');
        $this->addSql('ALTER TABLE course_objective_x_mesh DROP FOREIGN KEY FK_16291D94F28231CE');
        $this->addSql('ALTER TABLE course_objective_x_mesh ADD CONSTRAINT FK_16291D94CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course_objective_x_mesh ADD CONSTRAINT FK_16291D94F28231CE FOREIGN KEY (course_objective_id) REFERENCES course_x_objective (course_objective_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE program_year_objective_x_mesh DROP FOREIGN KEY FK_5FD56ABEBA83A669');
        $this->addSql('ALTER TABLE program_year_objective_x_mesh DROP FOREIGN KEY FK_5FD56ABECDB3C93B');
        $this->addSql('ALTER TABLE program_year_objective_x_mesh ADD CONSTRAINT FK_5FD56ABEBA83A669 FOREIGN KEY (program_year_objective_id) REFERENCES program_year_x_objective (program_year_objective_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE program_year_objective_x_mesh ADD CONSTRAINT FK_5FD56ABECDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE objective_x_mesh DROP FOREIGN KEY FK_936D667473484933');
        $this->addSql('ALTER TABLE objective_x_mesh DROP FOREIGN KEY FK_936D6674CDB3C93B');
        $this->addSql('ALTER TABLE objective_x_mesh ADD CONSTRAINT FK_936D667473484933 FOREIGN KEY (objective_id) REFERENCES objective (objective_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE objective_x_mesh ADD CONSTRAINT FK_936D6674CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE course_objective_x_mesh DROP FOREIGN KEY FK_16291D94F28231CE');
        $this->addSql('ALTER TABLE course_objective_x_mesh DROP FOREIGN KEY FK_16291D94CDB3C93B');
        $this->addSql('ALTER TABLE course_objective_x_mesh ADD CONSTRAINT FK_16291D94F28231CE FOREIGN KEY (course_objective_id) REFERENCES course_x_objective (course_objective_id)');
        $this->addSql('ALTER TABLE course_objective_x_mesh ADD CONSTRAINT FK_16291D94CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid)');
        $this->addSql('ALTER TABLE objective_x_mesh DROP FOREIGN KEY FK_936D667473484933');
        $this->addSql('ALTER TABLE objective_x_mesh DROP FOREIGN KEY FK_936D6674CDB3C93B');
        $this->addSql('ALTER TABLE objective_x_mesh ADD CONSTRAINT FK_936D667473484933 FOREIGN KEY (objective_id) REFERENCES objective (objective_id)');
        $this->addSql('ALTER TABLE objective_x_mesh ADD CONSTRAINT FK_936D6674CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid)');
        $this->addSql('ALTER TABLE program_year_objective_x_mesh DROP FOREIGN KEY FK_5FD56ABEBA83A669');
        $this->addSql('ALTER TABLE program_year_objective_x_mesh DROP FOREIGN KEY FK_5FD56ABECDB3C93B');
        $this->addSql('ALTER TABLE program_year_objective_x_mesh ADD CONSTRAINT FK_5FD56ABEBA83A669 FOREIGN KEY (program_year_objective_id) REFERENCES program_year_x_objective (program_year_objective_id)');
        $this->addSql('ALTER TABLE program_year_objective_x_mesh ADD CONSTRAINT FK_5FD56ABECDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid)');
        $this->addSql('ALTER TABLE session_objective_x_mesh DROP FOREIGN KEY FK_B33DC189BDD5F4B2');
        $this->addSql('ALTER TABLE session_objective_x_mesh DROP FOREIGN KEY FK_B33DC189CDB3C93B');
        $this->addSql('ALTER TABLE session_objective_x_mesh ADD CONSTRAINT FK_B33DC189BDD5F4B2 FOREIGN KEY (session_objective_id) REFERENCES session_x_objective (session_objective_id)');
        $this->addSql('ALTER TABLE session_objective_x_mesh ADD CONSTRAINT FK_B33DC189CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid)');
    }
}
