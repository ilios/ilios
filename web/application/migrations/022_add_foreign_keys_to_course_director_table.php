<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Enforce referential integrity on the course_director table by adding foreign keys and a combined primary key.
 */
class Migration_Add_foreign_keys_to_course_director_table extends CI_Migration
{
    /**
     * @see CI_Migration::up()
     */
    public function up ()
    {
        $this->db->trans_start();
        // remove orphaned entries first
        $sql =<<< EOL
DELETE FROM `course_director`
WHERE `user_id` NOT IN (
   SELECT `user_id` FROM `user`
) OR `course_id` NOT IN (
   SELECT `course_id` FROM `course`
)
EOL;
        $this->db->query($sql);
        // add keys
        $sql =<<< EOL
ALTER IGNORE TABLE `course_director`
    ADD PRIMARY KEY (`course_id`, `user_id`),
    ADD CONSTRAINT `fkey_course_director_course_id`
        FOREIGN KEY (`course_id`)
        REFERENCES `course` (`course_id`)
        ON UPDATE CASCADE ON DELETE CASCADE,
    ADD CONSTRAINT `fkey_course_director_user_id`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`user_id`)
        ON UPDATE CASCADE ON DELETE CASCADE
EOL;
        $this->db->query($sql);
        $this->db->trans_complete();
    }

    /**
     * @see CI_Migration::down()
     */
    public function down ()
    {
        $this->db->trans_start();
        $sql =<<< EOL
ALTER TABLE `course_director`
    DROP INDEX `fkey_course_director_user_id`,
    DROP FOREIGN KEY `fkey_course_director_user_id`,
    DROP FOREIGN KEY `fkey_course_director_course_id`,
    DROP PRIMARY KEY
EOL;
        $this->db->query($sql);
        $this->db->trans_complete();
    }
}
