<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This gets rid of the unused "audit_content" table, and rolls the "audit_event" and "audit_atom" tables into one.
 */
class Migration_Chop_auditing_tables extends CI_Migration
{

    public function up()
    {
        // @todo implement

    }

    public function down()
    {
        // @todo implement
    }
}
