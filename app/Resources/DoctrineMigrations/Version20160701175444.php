<?php

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Renames the group table to learner_group, as well as any corresponding join tables and primary/fkey columns.
 */
class Version20160701175444 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSQL('RENAME TABLE `group` TO learner_group');
        $this->addSQL('RENAME TABLE group_x_instructor TO learner_group_x_instructor');
        $this->addSQL('RENAME TABLE group_x_instructor_group TO learner_group_x_instructor_group');
        $this->addSQL('RENAME TABLE group_x_user TO learner_group_x_user');
        $this->addSQL('RENAME TABLE ilm_session_facet_x_group TO ilm_session_facet_x_learner_group');
        $this->addSql('RENAME TABLE offering_x_group TO offering_x_learner_group');

        $this->addSql('ALTER TABLE offering_x_learner_group DROP FOREIGN KEY FK_4D68848FFE54D947');
        $this->addSql('ALTER TABLE offering_x_learner_group DROP FOREIGN KEY FK_4D68848F8EDF74F0');
        $this->addSql('DROP INDEX idx_4d68848f8edf74f0 ON offering_x_learner_group');
        $this->addSql('DROP INDEX idx_4d68848ffe54d947 ON offering_x_learner_group');
        $this->addSql('ALTER TABLE ilm_session_facet_x_learner_group DROP FOREIGN KEY FK_B43B41DCFE54D947');
        $this->addSql('ALTER TABLE ilm_session_facet_x_learner_group DROP FOREIGN KEY FK_B43B41DC504270C1');
        $this->addSql('DROP INDEX idx_b43b41dc504270c1 ON ilm_session_facet_x_learner_group');
        $this->addSql('DROP INDEX idx_b43b41dcfe54d947 ON ilm_session_facet_x_learner_group');
        $this->addSql('ALTER TABLE learner_group DROP FOREIGN KEY FK_6DC044C561997596');
        $this->addSql('ALTER TABLE learner_group DROP FOREIGN KEY FK_6DC044C535983C93');
        $this->addSql('DROP INDEX idx_6dc044c535983c93 ON learner_group');
        $this->addSql('DROP INDEX idx_6dc044c561997596 ON learner_group');
        $this->addSql('ALTER TABLE learner_group_x_instructor_group DROP FOREIGN KEY FK_49AFEA21FE54D947');
        $this->addSql('ALTER TABLE learner_group_x_instructor_group DROP FOREIGN KEY FK_49AFEA21FE367BE2');
        $this->addSql('DROP INDEX idx_49afea21fe54d947 ON learner_group_x_instructor_group');
        $this->addSql('DROP INDEX idx_49afea21fe367be2 ON learner_group_x_instructor_group');
        $this->addSql('ALTER TABLE learner_group_x_user DROP FOREIGN KEY FK_93A1A790FE54D947');
        $this->addSql('ALTER TABLE learner_group_x_user DROP FOREIGN KEY FK_93A1A790A76ED395');
        $this->addSql('DROP INDEX idx_93a1a790fe54d947 ON learner_group_x_user');
        $this->addSql('DROP INDEX idx_93a1a790a76ed395 ON learner_group_x_user');
        $this->addSql('ALTER TABLE learner_group_x_instructor DROP FOREIGN KEY FK_8CE57915FE54D947');
        $this->addSql('ALTER TABLE learner_group_x_instructor DROP FOREIGN KEY FK_8CE57915A76ED395');
        $this->addSql('DROP INDEX idx_8ce57915fe54d947 ON learner_group_x_instructor');
        $this->addSql('DROP INDEX idx_8ce57915a76ed395 ON learner_group_x_instructor');

        $this->addSql('ALTER TABLE learner_group CHANGE group_id learner_group_id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE offering_x_learner_group CHANGE group_id learner_group_id INT NOT NULL');
        $this->addSql('ALTER TABLE ilm_session_facet_x_learner_group CHANGE group_id learner_group_id INT NOT NULL');
        $this->addSql('ALTER TABLE learner_group_x_instructor_group CHANGE group_id learner_group_id INT NOT NULL');
        $this->addSql('ALTER TABLE learner_group_x_user CHANGE group_id learner_group_id INT NOT NULL');
        $this->addSql('ALTER TABLE learner_group_x_instructor CHANGE group_id learner_group_id INT NOT NULL');

        $this->addSql('CREATE INDEX IDX_2983E35A8EDF74F0 ON offering_x_learner_group (offering_id)');
        $this->addSql('CREATE INDEX IDX_2983E35AECDA11A2 ON offering_x_learner_group (learner_group_id)');
        $this->addSql('ALTER TABLE offering_x_learner_group ADD CONSTRAINT FK_2983E35AECDA11A2 FOREIGN KEY (learner_group_id) REFERENCES learner_group (learner_group_id)');
        $this->addSql('ALTER TABLE offering_x_learner_group ADD CONSTRAINT FK_4D68848F8EDF74F0 FOREIGN KEY (offering_id) REFERENCES offering (offering_id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_A331EF26504270C1 ON ilm_session_facet_x_learner_group (ilm_session_facet_id)');
        $this->addSql('CREATE INDEX IDX_A331EF26ECDA11A2 ON ilm_session_facet_x_learner_group (learner_group_id)');
        $this->addSql('ALTER TABLE ilm_session_facet_x_learner_group ADD CONSTRAINT FK_A331EF26ECDA11A2 FOREIGN KEY (learner_group_id) REFERENCES learner_group (learner_group_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ilm_session_facet_x_learner_group ADD CONSTRAINT FK_B43B41DC504270C1 FOREIGN KEY (ilm_session_facet_id) REFERENCES ilm_session_facet (ilm_session_facet_id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_694D1E7335983C93 ON learner_group (cohort_id)');
        $this->addSql('CREATE INDEX IDX_694D1E7361997596 ON learner_group (parent_group_id)');
        $this->addSql('ALTER TABLE learner_group ADD CONSTRAINT FK_694D1E7358466391 FOREIGN KEY (parent_group_id) REFERENCES learner_group (learner_group_id)');
        $this->addSql('ALTER TABLE learner_group ADD CONSTRAINT FK_6DC044C535983C93 FOREIGN KEY (cohort_id) REFERENCES cohort (cohort_id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_810CB9F6ECDA11A2 ON learner_group_x_instructor_group (learner_group_id)');
        $this->addSql('CREATE INDEX IDX_810CB9F6FE367BE2 ON learner_group_x_instructor_group (instructor_group_id)');
        $this->addSql('ALTER TABLE learner_group_x_instructor_group ADD CONSTRAINT FK_810CB9F6ECDA11A2 FOREIGN KEY (learner_group_id) REFERENCES learner_group (learner_group_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE learner_group_x_instructor_group ADD CONSTRAINT FK_49AFEA21FE367BE2 FOREIGN KEY (instructor_group_id) REFERENCES instructor_group (instructor_group_id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_BB0515EFECDA11A2 ON learner_group_x_user (learner_group_id)');
        $this->addSql('CREATE INDEX IDX_BB0515EFA76ED395 ON learner_group_x_user (user_id)');
        $this->addSql('ALTER TABLE learner_group_x_user ADD CONSTRAINT FK_BB0515EFECDA11A2 FOREIGN KEY (learner_group_id) REFERENCES learner_group (learner_group_id)');
        $this->addSql('ALTER TABLE learner_group_x_user ADD CONSTRAINT FK_93A1A790A76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id)');
        $this->addSql('CREATE INDEX IDX_E8C1047FECDA11A2 ON learner_group_x_instructor (learner_group_id)');
        $this->addSql('CREATE INDEX IDX_E8C1047FA76ED395 ON learner_group_x_instructor (user_id)');
        $this->addSql('ALTER TABLE learner_group_x_instructor ADD CONSTRAINT FK_E8C1047FECDA11A2 FOREIGN KEY (learner_group_id) REFERENCES learner_group (learner_group_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE learner_group_x_instructor ADD CONSTRAINT FK_8CE57915A76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id) ON DELETE CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('RENAME TABLE offering_x_learner_group TO offering_x_group');
        $this->addSQL('RENAME TABLE ilm_session_facet_x_learner_group TO ilm_session_facet_x_group');
        $this->addSQL('RENAME TABLE learner_group_x_user TO group_x_user');
        $this->addSQL('RENAME TABLE learner_group_x_instructor_group TO group_x_instructor_group');
        $this->addSQL('RENAME TABLE learner_group_x_instructor TO group_x_instructor');
        $this->addSQL('RENAME TABLE learner_group TO `group`');

        $this->addSql('ALTER TABLE ilm_session_facet_x_group DROP FOREIGN KEY FK_B43B41DC504270C1');
        $this->addSql('ALTER TABLE ilm_session_facet_x_group DROP FOREIGN KEY FK_A331EF26ECDA11A2');
        $this->addSql('DROP INDEX idx_a331ef26504270c1 ON ilm_session_facet_x_group');
        $this->addSql('DROP INDEX idx_a331ef26ecda11a2 ON ilm_session_facet_x_group');
        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_6DC044C535983C93');
        $this->addSql('ALTER TABLE `group` DROP FOREIGN KEY FK_694D1E7358466391');
        $this->addSql('DROP INDEX idx_694d1e7361997596 ON `group`');
        $this->addSql('DROP INDEX idx_694d1e7335983c93 ON `group`');
        $this->addSql('ALTER TABLE group_x_instructor DROP FOREIGN KEY FK_E8C1047FECDA11A2');
        $this->addSql('ALTER TABLE group_x_instructor DROP FOREIGN KEY FK_8CE57915A76ED395');
        $this->addSql('DROP INDEX idx_e8c1047fecda11a2 ON group_x_instructor');
        $this->addSql('DROP INDEX idx_e8c1047fa76ed395 ON group_x_instructor');
        $this->addSql('ALTER TABLE group_x_instructor_group DROP FOREIGN KEY FK_810CB9F6ECDA11A2');
        $this->addSql('ALTER TABLE group_x_instructor_group DROP FOREIGN KEY FK_49AFEA21FE367BE2');
        $this->addSql('DROP INDEX idx_810cb9f6ecda11a2 ON group_x_instructor_group');
        $this->addSql('DROP INDEX idx_810cb9f6fe367be2 ON group_x_instructor_group');
        $this->addSql('ALTER TABLE group_x_user DROP FOREIGN KEY FK_BB0515EFECDA11A2');
        $this->addSql('ALTER TABLE group_x_user DROP FOREIGN KEY FK_93A1A790A76ED395');
        $this->addSql('DROP INDEX idx_bb0515efecda11a2 ON group_x_user');
        $this->addSql('DROP INDEX idx_bb0515efa76ed395 ON group_x_user');
        $this->addSql('ALTER TABLE offering_x_group DROP FOREIGN KEY FK_4D68848F8EDF74F0');
        $this->addSql('ALTER TABLE offering_x_group DROP FOREIGN KEY FK_2983E35AECDA11A2');
        $this->addSql('DROP INDEX idx_2983e35a8edf74f0 ON offering_x_group');
        $this->addSql('DROP INDEX idx_2983e35aecda11a2 ON offering_x_group');

        $this->addSql('ALTER TABLE `group` CHANGE learner_group_id group_id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE offering_x_group CHANGE learner_group_id group_id INT(11) NOT NULL');
        $this->addSql('ALTER TABLE ilm_session_facet_x_group CHANGE learner_group_id group_id INT(11) NOT NULL');
        $this->addSql('ALTER TABLE group_x_instructor_group CHANGE learner_group_id group_id INT(11) NOT NULL');
        $this->addSql('ALTER TABLE group_x_user CHANGE learner_group_id group_id INT(11) NOT NULL');
        $this->addSql('ALTER TABLE group_x_instructor CHANGE learner_group_id group_id INT(11) NOT NULL');

        $this->addSql('CREATE INDEX IDX_B43B41DC504270C1 ON ilm_session_facet_x_group (ilm_session_facet_id)');
        $this->addSql('CREATE INDEX IDX_B43B41DCFE54D947 ON ilm_session_facet_x_group (group_id)');
        $this->addSql('ALTER TABLE ilm_session_facet_x_group ADD CONSTRAINT FK_B43B41DC504270C1 FOREIGN KEY (ilm_session_facet_id) REFERENCES ilm_session_facet (ilm_session_facet_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ilm_session_facet_x_group ADD CONSTRAINT FK_B43B41DCFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (group_id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_6DC044C561997596 ON `group` (parent_group_id)');
        $this->addSql('CREATE INDEX IDX_6DC044C535983C93 ON `group` (cohort_id)');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C535983C93 FOREIGN KEY (cohort_id) REFERENCES cohort (cohort_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `group` ADD CONSTRAINT FK_6DC044C561997596 FOREIGN KEY (parent_group_id) REFERENCES `group` (group_id)');
        $this->addSql('CREATE INDEX IDX_8CE57915FE54D947 ON group_x_instructor (group_id)');
        $this->addSql('CREATE INDEX IDX_8CE57915A76ED395 ON group_x_instructor (user_id)');
        $this->addSql('ALTER TABLE group_x_instructor ADD CONSTRAINT FK_8CE57915FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (group_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_x_instructor ADD CONSTRAINT FK_8CE57915A76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_49AFEA21FE54D947 ON group_x_instructor_group (group_id)');
        $this->addSql('CREATE INDEX IDX_49AFEA21FE367BE2 ON group_x_instructor_group (instructor_group_id)');
        $this->addSql('ALTER TABLE group_x_instructor_group ADD CONSTRAINT FK_49AFEA21FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (group_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_x_instructor_group ADD CONSTRAINT FK_49AFEA21FE367BE2 FOREIGN KEY (instructor_group_id) REFERENCES instructor_group (instructor_group_id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_93A1A790FE54D947 ON group_x_user (group_id)');
        $this->addSql('CREATE INDEX IDX_93A1A790A76ED395 ON group_x_user (user_id)');
        $this->addSql('ALTER TABLE group_x_user ADD CONSTRAINT FK_93A1A790FE54D947 FOREIGN KEY (group_id) REFERENCES `group` (group_id)');
        $this->addSql('ALTER TABLE group_x_user ADD CONSTRAINT FK_93A1A790A76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id)');
        $this->addSql('CREATE INDEX IDX_4D68848F8EDF74F0 ON offering_x_group (offering_id)');
        $this->addSql('CREATE INDEX IDX_4D68848FFE54D947 ON offering_x_group (group_id)');
        $this->addSql('ALTER TABLE offering_x_group ADD CONSTRAINT FK_4D68848F8EDF74F0 FOREIGN KEY (offering_id) REFERENCES offering (offering_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offering_x_group ADD CONSTRAINT FK_4D68848FFE54D947 FOREIGN KEY (group_id) REFERENCES `group` (group_id)');
    }
}
