<?php

declare(strict_types=1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Schema\Schema;
use App\Classes\MysqlMigration;

/**
 * Adds relationships to x-objectives, based on how objectives are wired up.
 */
final class Version20200630182857 extends MysqlMigration
{
    public function getDescription() : string
    {
        return 'Adds relationships to x-objectives, based on how objectives are wired up.';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE course_objective_x_program_year_objective (course_objective_id INT NOT NULL, program_year_objective_id INT NOT NULL, INDEX IDX_CB20F416F28231CE (course_objective_id), INDEX IDX_CB20F416BA83A669 (program_year_objective_id), PRIMARY KEY(course_objective_id, program_year_objective_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE course_objective_x_mesh (course_objective_id INT NOT NULL, mesh_descriptor_uid VARCHAR(12) NOT NULL, INDEX IDX_16291D94F28231CE (course_objective_id), INDEX IDX_16291D94CDB3C93B (mesh_descriptor_uid), PRIMARY KEY(course_objective_id, mesh_descriptor_uid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE program_year_objective_x_mesh (program_year_objective_id INT NOT NULL, mesh_descriptor_uid VARCHAR(12) NOT NULL, INDEX IDX_5FD56ABEBA83A669 (program_year_objective_id), INDEX IDX_5FD56ABECDB3C93B (mesh_descriptor_uid), PRIMARY KEY(program_year_objective_id, mesh_descriptor_uid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session_objective_x_course_objective (session_objective_id INT NOT NULL, course_objective_id INT NOT NULL, INDEX IDX_5EB8C49DBDD5F4B2 (session_objective_id), INDEX IDX_5EB8C49DF28231CE (course_objective_id), PRIMARY KEY(session_objective_id, course_objective_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session_objective_x_mesh (session_objective_id INT NOT NULL, mesh_descriptor_uid VARCHAR(12) NOT NULL, INDEX IDX_B33DC189BDD5F4B2 (session_objective_id), INDEX IDX_B33DC189CDB3C93B (mesh_descriptor_uid), PRIMARY KEY(session_objective_id, mesh_descriptor_uid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE course_objective_x_program_year_objective ADD CONSTRAINT FK_CB20F416F28231CE FOREIGN KEY (course_objective_id) REFERENCES course_x_objective (course_objective_id)');
        $this->addSql('ALTER TABLE course_objective_x_program_year_objective ADD CONSTRAINT FK_CB20F416BA83A669 FOREIGN KEY (program_year_objective_id) REFERENCES program_year_x_objective (program_year_objective_id)');
        $this->addSql('ALTER TABLE course_objective_x_mesh ADD CONSTRAINT FK_16291D94F28231CE FOREIGN KEY (course_objective_id) REFERENCES course_x_objective (course_objective_id)');
        $this->addSql('ALTER TABLE course_objective_x_mesh ADD CONSTRAINT FK_16291D94CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid)');
        $this->addSql('ALTER TABLE program_year_objective_x_mesh ADD CONSTRAINT FK_5FD56ABEBA83A669 FOREIGN KEY (program_year_objective_id) REFERENCES program_year_x_objective (program_year_objective_id)');
        $this->addSql('ALTER TABLE program_year_objective_x_mesh ADD CONSTRAINT FK_5FD56ABECDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid)');
        $this->addSql('ALTER TABLE session_objective_x_course_objective ADD CONSTRAINT FK_5EB8C49DBDD5F4B2 FOREIGN KEY (session_objective_id) REFERENCES session_x_objective (session_objective_id)');
        $this->addSql('ALTER TABLE session_objective_x_course_objective ADD CONSTRAINT FK_5EB8C49DF28231CE FOREIGN KEY (course_objective_id) REFERENCES course_x_objective (course_objective_id)');
        $this->addSql('ALTER TABLE session_objective_x_mesh ADD CONSTRAINT FK_B33DC189BDD5F4B2 FOREIGN KEY (session_objective_id) REFERENCES session_x_objective (session_objective_id)');
        $this->addSql('ALTER TABLE session_objective_x_mesh ADD CONSTRAINT FK_B33DC189CDB3C93B FOREIGN KEY (mesh_descriptor_uid) REFERENCES mesh_descriptor (mesh_descriptor_uid)');
        $this->addSql('ALTER TABLE course_x_objective ADD ancestor_id INT DEFAULT NULL, ADD title LONGTEXT NOT NULL, ADD active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE course_x_objective ADD CONSTRAINT FK_4C880AE4C671CEA1 FOREIGN KEY (ancestor_id) REFERENCES course_x_objective (course_objective_id)');
        $this->addSql('CREATE INDEX IDX_4C880AE4C671CEA1 ON course_x_objective (ancestor_id)');
        $this->addSql('ALTER TABLE program_year_x_objective ADD competency_id INT DEFAULT NULL, ADD ancestor_id INT DEFAULT NULL, ADD title LONGTEXT NOT NULL, ADD active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE program_year_x_objective ADD CONSTRAINT FK_FF29E643FB9F58C FOREIGN KEY (competency_id) REFERENCES competency (competency_id)');
        $this->addSql('ALTER TABLE program_year_x_objective ADD CONSTRAINT FK_FF29E643C671CEA1 FOREIGN KEY (ancestor_id) REFERENCES program_year_x_objective (program_year_objective_id)');
        $this->addSql('CREATE INDEX IDX_FF29E643FB9F58C ON program_year_x_objective (competency_id)');
        $this->addSql('CREATE INDEX IDX_FF29E643C671CEA1 ON program_year_x_objective (ancestor_id)');
        $this->addSql('ALTER TABLE session_x_objective ADD ancestor_id INT DEFAULT NULL, ADD title LONGTEXT NOT NULL, ADD active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE session_x_objective ADD CONSTRAINT FK_C4BF2447C671CEA1 FOREIGN KEY (ancestor_id) REFERENCES session_x_objective (session_objective_id)');
        $this->addSql('CREATE INDEX IDX_C4BF2447C671CEA1 ON session_x_objective (ancestor_id)');

        // ---------------------------
        // data migration
        // ---------------------------
        // copy title and active flag from objectives to linked x-objectives
        $this->addSql('UPDATE session_x_objective sxo JOIN objective o ON o.objective_id = sxo.objective_id SET sxo.title = o.title, sxo.active = o.active');
        $this->addSql('UPDATE course_x_objective cxo JOIN objective o ON o.objective_id = cxo.objective_id SET cxo.title = o.title, cxo.active = o.active');
        $this->addSql('UPDATE program_year_x_objective pyxo JOIN objective o ON o.objective_id = pyxo.objective_id SET pyxo.title = o.title, pyxo.active = o.active, pyxo.competency_id = o.competency_id');

        // copy mesh associations
        $this->addSql('INSERT INTO session_objective_x_mesh (session_objective_id, mesh_descriptor_uid) SELECT sxo.session_objective_id, oxm.mesh_descriptor_uid FROM session_x_objective sxo JOIN objective o ON sxo.objective_id = o.objective_id JOIN objective_x_mesh oxm ON o.objective_id = oxm.objective_id');
        $this->addSql('INSERT INTO course_objective_x_mesh (course_objective_id, mesh_descriptor_uid) SELECT cxo.course_objective_id, oxm.mesh_descriptor_uid FROM course_x_objective cxo JOIN objective o ON cxo.objective_id = o.objective_id JOIN objective_x_mesh oxm ON o.objective_id = oxm.objective_id');
        $this->addSql('INSERT INTO program_year_objective_x_mesh (program_year_objective_id, mesh_descriptor_uid) SELECT pyxo.program_year_objective_id, oxm.mesh_descriptor_uid FROM program_year_x_objective pyxo JOIN objective o ON pyxo.objective_id = o.objective_id JOIN objective_x_mesh oxm ON o.objective_id = oxm.objective_id');

        // copy parentage associations
        $this->addSql('INSERT INTO session_objective_x_course_objective(session_objective_id, course_objective_id) SELECT sxo.session_objective_id, cxo.course_objective_id FROM session_x_objective sxo JOIN objective o ON sxo.objective_id = o.objective_id JOIN objective_x_objective oxo ON o.objective_id = oxo.objective_id JOIN objective o2 ON o2.objective_id = oxo.parent_objective_id JOIN course_x_objective cxo ON o2.objective_id = cxo.objective_id');
        $this->addSql('INSERT INTO course_objective_x_program_year_objective(course_objective_id, program_year_objective_id) SELECT cxo.course_objective_id, pyxo.program_year_objective_id FROM course_x_objective cxo JOIN objective o ON cxo.objective_id = o.objective_id JOIN objective_x_objective oxo ON o.objective_id = oxo.objective_id JOIN objective o2 ON o2.objective_id = oxo.parent_objective_id JOIN program_year_x_objective pyxo ON o2.objective_id = pyxo.objective_id');

        // copy ancestry associations
        $this->addSql('UPDATE session_x_objective sxo JOIN objective o ON o.objective_id = sxo.objective_id JOIN objective ancestor ON o.ancestor_id = ancestor.objective_id JOIN session_x_objective sxo2 ON sxo2.objective_id = ancestor.objective_id SET sxo.ancestor_id = sxo2.session_objective_id');
        $this->addSql('UPDATE course_x_objective cxo JOIN objective o ON o.objective_id = cxo.objective_id JOIN objective ancestor ON o.ancestor_id = ancestor.objective_id JOIN course_x_objective cxo2 ON cxo2.objective_id = ancestor.objective_id SET cxo.ancestor_id = cxo2.course_objective_id');
        $this->addSql('UPDATE program_year_x_objective pyxo JOIN objective o ON o.objective_id = pyxo.objective_id JOIN objective ancestor ON o.ancestor_id = ancestor.objective_id JOIN program_year_x_objective pyxo2 ON pyxo2.objective_id = ancestor.objective_id SET pyxo.ancestor_id = pyxo2.program_year_objective_id');

        // reverse data migration: copy position BACK onto objective (see App\Migrations\Version20200408232411.php)
        $this->addSql('UPDATE objective o JOIN session_x_objective sxo ON o.objective_id = sxo.objective_id SET o.position = sxo.position');
        $this->addSql('UPDATE objective o JOIN course_x_objective cxo ON o.objective_id = cxo.objective_id SET o.position = cxo.position');
        $this->addSql('UPDATE objective o JOIN program_year_x_objective pyxo ON o.objective_id = pyxo.objective_id SET o.position = pyxo.position');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE course_objective_x_program_year_objective');
        $this->addSql('DROP TABLE course_objective_x_mesh');
        $this->addSql('DROP TABLE program_year_objective_x_mesh');
        $this->addSql('DROP TABLE session_objective_x_course_objective');
        $this->addSql('DROP TABLE session_objective_x_mesh');
        $this->addSql('ALTER TABLE course_x_objective DROP FOREIGN KEY FK_4C880AE4C671CEA1');
        $this->addSql('DROP INDEX IDX_4C880AE4C671CEA1 ON course_x_objective');
        $this->addSql('ALTER TABLE course_x_objective DROP ancestor_id, DROP title, DROP active');
        $this->addSql('ALTER TABLE program_year_x_objective DROP FOREIGN KEY FK_FF29E643FB9F58C');
        $this->addSql('ALTER TABLE program_year_x_objective DROP FOREIGN KEY FK_FF29E643C671CEA1');
        $this->addSql('DROP INDEX IDX_FF29E643FB9F58C ON program_year_x_objective');
        $this->addSql('DROP INDEX IDX_FF29E643C671CEA1 ON program_year_x_objective');
        $this->addSql('ALTER TABLE program_year_x_objective DROP competency_id, DROP ancestor_id, DROP title, DROP active');
        $this->addSql('ALTER TABLE session_x_objective DROP FOREIGN KEY FK_C4BF2447C671CEA1');
        $this->addSql('DROP INDEX IDX_C4BF2447C671CEA1 ON session_x_objective');
        $this->addSql('ALTER TABLE session_x_objective DROP ancestor_id, DROP title, DROP active');
    }
}
