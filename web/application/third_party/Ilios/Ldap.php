<?php

/**
 * Wrapper class around LDAP connection/bind.
 */
class Ilios_Ldap
{
    /**
     * Flag, indicates a search scope of the base entry and entries one level below
     * @var int
     */
    const LDAP_SCOPE_ONELEVEL = 0;

    /**
     * Flag, indicates a search scope of the base entry and all entries in sub-levels
     * @var int
     */
    const LDAP_SCOPE_SUBTREE = 1;

    /**
     * default LDAP server port
     * @var int
     */
    const DEFAULT_PORT = 389;

    /**
     * LDAP configuration options.
     * Processed values include
     * ['host'] ... server-name or URL | mandatory
     * ['port'] ... server-port | optional, see constant DEFAULT_PORT for default-value
     * ['bind_dn'] ... bind DN | mandatory for non-anonymous connections
     * ['password'] ... password | mandatory for non-anonymous connections
     * ['protocol'] ... LDAP protocol (e.g. 'ldaps') | optional, only needed if 'host' value does not include the protocol.
     * @var array
     */
    protected $_options;

    /**
     * LDAP link identifier
     * @var resource
     */
    protected $_ldap;


    public function __construct (array $options = array())
    {
        $this->_options = $options;
    }

    /**
     * Connects to LDAP server.
     * @return Ilios_Ldap itself
     * @throws Ilios_Ldap_Exception
     */
    public function connect ()
    {
        $port = self::DEFAULT_PORT;
        if (array_key_exists('port', $this->_options) && 0 < (int) $this->_options['port']) {
            $port = (int) $this->_options['port'];
        }

        $host = array_key_exists('host', $this->_options) ? trim($this->_options['host']) : '';

        if ('' === $host) {
            throw new Ilios_Ldap_Exception("Couldn't connect  - LDAP server hostname missing.");
        }

        $hostIsUrl = false;
        $matches = array();
        // check if $host contains an URL.
        if (0 < preg_match_all('/^ldap(?:i|s)?:\/\//', $host, $matches)) {
          $hostIsUrl = true;
        }
        // disconnect before reconnecting
        $this->disconnect();

        // connect
        // only pass the port if $host is not an URL
        $ldap = (false === $hostIsUrl) ? @ldap_connect($host, $port) : @ldap_connect($host);

        if (is_resource($ldap)) { // success!
            $this->_ldap = $ldap;
            @ldap_set_option($this->_ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
            return $this; // return
        }

        // failed to connect
        $errorNumber = @ldap_errno($ldap);
        $errorMsg = "Failed to connect to {$host}:{$port}: " . ldap_err2str($errorNumber) . " (#{$errorNumber})";
        $this->disconnect();
        throw new Ilios_Ldap_Exception($errorMsg, $errorNumber);
    }
    /**
     * @throws Ilios_Ldap_Exception
     */
    public function bind ()
    {
        if (! is_resource($this->_ldap)) {
            $this->connect();
        }

        $bindDn = array_key_exists('bind_dn', $this->_options) ? $this->_options['bind_dn'] : null;
        $password = array_key_exists('password', $this->_options) ? $this->_options['password'] : null;
        if (true === @ldap_bind($this->_ldap, $bindDn, $password)) {
            return $this;
        }

        $errorNumber = @ldap_errno($this->_ldap);
        $errorMsg = "LDAP bind failed: " . ldap_err2str($errorNumber) . " (#{$errorNumber})";
        $this->disconnect();
        throw new Ilios_Ldap_Exception($errorMsg, $errorNumber);
    }

    /**
     * Disconnects from LDAP server.
     */
    public function disconnect ()
    {
        if (is_resource($this->_ldap)) {
            @ldap_unbind($this->_ldap);
        }
        $this->_ldap = null;
    }

    /**
     * Destructor.
     */
    public function __destruct ()
    {
        $this->disconnect();
    }

    /**
     * Performs a LDAP search.
     * @param string $baseDn
     * @param string $filter
     * @param int $scope
     * @param array $attributes
     * @param boolean $attrOnly
     * @param int $limit
     * @param int $timeout
     * @return resource
     * @throws Ilios_Ldap_Exception
     */
    public function search ($baseDn, $filter, $scope = self::LDAP_SCOPE_SUBTREE, array $attributes = array(),
                    $attrOnly = false, $limit = 0, $timeout = 0)
    {
        $result = false;
        if (! is_resource($this->_ldap)) {
            $this->bind();
        }
        switch ($scope) {
            case self::LDAP_SCOPE_SUBTREE :
                $result = @ldap_search($this->_ldap, $baseDn, $filter, $attributes, (int) $attrOnly, $limit, $timeout);
                break;
            case self::LDAP_SCOPE_ONELEVEL :
            default :
                $result = @ldap_list($this->_ldap, $baseDn, $filter, $attributes, (int) $attrOnly, $limit, $timeout);
        }
        if (false === $result) {
            $errorNumber = @ldap_errno($this->_ldap);
            throw new Ilios_Ldap_Exception('LDAP search failed: ' . ldap_err2str($errorNumber) . " (#{$errorNumber})", $errorNumber);
        }
        return $result;
    }

    /**
     * Returns the internal LDAP link identifier.
     * @return resource
     */
    public function getResource ()
    {
        return $this->_ldap;
    }
}
