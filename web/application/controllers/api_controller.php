<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'ilios_web_controller.php';

/**
 * @package Ilios
 * API key controller
 */
class API_Controller extends Ilios_Web_Controller
{
    /**
     * Constructor
     */
    public function __construct ()
    {
        parent::__construct();
        $this->load->model('Authentication', 'authentication', true);
        $this->load->model('User', 'user', TRUE);
    }

    /**
     * Index action.
     */
    public function index ()
    {
        $this->get_api_key();
    }

    /**
     * Fetch the user's API key. If none exists, create one
     */
    public function get_api_key ()
    {
        $key = @$this->authentication->getByUserId($this->session->userdata('uid'))->api_key;
        if ($key) {
            header('Content-type: text/plain');
            print $key;
        } else {
            $this->new_api_key();
        }
    }

    /**
     * Create and store a new API key for the user
     */
    public function new_api_key ()
    {
        if ($this->session->userdata('uid')) {
            if (function_exists('openssl_random_pseudo_bytes')) {
                $key = bin2hex(openssl_random_pseudo_bytes(32));
            } else {
                $key = '';
                for ($i=0;$i<32;$i++) {
                    $key = $key . str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
                }
            }
            header('Content-type: text/plain');
            if ($this->authentication->changeAPIKey($this->session->userdata('uid'), $key)) {
                print $key;
            } else {
                print 'Error';
            }
        } else {
            header('HTTP/1.1 403 Forbidden');
        }
    }
}
