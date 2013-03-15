<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * The sole purpose of this migration is to provide a place
 * to downgrade to from the first "real" migration.
 */
class Migration_Do_nothing extends CI_Migration
{
    public function up ()
    {
        // do nothing
    }

    public function down ()
    {
        // do nothing
    }
}