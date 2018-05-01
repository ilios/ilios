<?php declare(strict_types = 1);

namespace Ilios\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Assigns users into new roles based on their current explicit write permissions and legacy roles.
 * ACHTUNG! This migration cannot be rolled back!
 */
class Version20180313211111 extends AbstractMigration
{
    /**
     * @inheritdoc
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // migrate developers
        $sql=<<<EOL
INSERT IGNORE INTO school_administrator (school_id, user_id) (
  SELECT DISTINCT
    user.school_id,
    user.user_id
  FROM user
    JOIN user_x_user_role u ON user.user_id = u.user_id
    JOIN user_role r ON u.user_role_id = r.user_role_id
  WHERE r.title = 'Developer'
)
EOL;
        $this->addSql($sql);

        // migrate users with write permissions to course X
        $sql=<<<EOL
INSERT IGNORE INTO course_administrator (course_id, user_id) (
  SELECT DISTINCT
    course.course_id,
    user.user_id
  FROM user
    JOIN permission p ON user.user_id = p.user_id
    JOIN course ON course.course_id = p.table_row_id
  WHERE p.can_write = 1 AND p.table_name = 'course'
);
EOL;
        $this->addSql($sql);
    }

    /**
     * @inheritdoc
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->throwIrreversibleMigrationException();
    }
}
