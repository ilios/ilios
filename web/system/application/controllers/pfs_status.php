<?php
/**
 * @package Ilios
 * 
 * This is the PrettyFeckinStupid_Status page; our health monitoring system here basically parses
 *  the <title> element of a returned HTML page for any text, in no agreed upon universal
 *  vocabulary.. what a great design
 */
class PFS_Status extends CI_Controller
{

    public function __construct ()
    {
        parent::__construct();
        $this->load->helper(array('string', 'form', 'url'));
        $this->load->database();
    }

    public function index ()
    {
        $msg = "I'M OK";
        $shibProcess = exec('ps uax | grep shibd | grep -v grep');

        echo "<html><head><title>";
        if (! ($this->db->count_all('user') > 0)) {
            $msg = "The database " . $this->db->database . " on " . $this->db->hostname
                            . " appears to be corrupt or unreachable.";
        }
        else if ($shibProcess == '') {
            $msg = "The local shibd process appears to be dead.";
        }
        echo $msg . "</title></head><body>" . $msg . "</body></html>";
    }
}
