<?php

namespace Ilios\AuthenticationBundle\Service;

use Ilios\CoreBundle\Service\Config;

/**
 * Class CasManager
 */
class CasManager
{
    /**
     * @var string
     */
    protected $casServer;

    /**
     * @var string
     */
    protected $casVersion;

    /**
     * @var boolean
     */
    protected $casVerifySSL;

    /**
     * @var string
     */
    protected $casCertificatePath;

    /**
     * Constructor
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->casServer = $config->get('cas_authentication_server');
        $this->casVersion = $config->get('cas_authentication_version');
        $this->casVerifySSL = $config->get('cas_authentication_verify_ssl');
        $this->casCertificatePath = $config->get('cas_authentication_certificate_path');
    }

    public function getLoginUrl()
    {
        $logoutUrl = $this->casServer . '/login';
        return $logoutUrl;
    }

    public function getLogoutUrl()
    {
        $logoutUrl = $this->casServer . '/logout';
        return $logoutUrl;
    }

    /**
     * Use a ticket to authenticate a user and get a userId
     *
     * @param string $service
     * @param string $ticket
     *
     * @return string $userId
     * @throws \Exception
     */
    public function getUserId($service, $ticket)
    {
        $url = $this->getUrl($service, $ticket);
        $root = $this->connect($url);

        if ($root->getElementsByTagName("authenticationSuccess")->length != 0) {
            // authentication succeeded, extract the user name
            $elements = $root->getElementsByTagName("authenticationSuccess");
            if ($elements->item(0)->getElementsByTagName("user")->length > 0) {
                return $elements->item(0)->getElementsByTagName("user")->item(0)->nodeValue;
            }
        } elseif ($root->getElementsByTagName("authenticationFailure")->length != 0) {
            $elements = $root->getElementsByTagName("authenticationFailure");
            $reason = $elements->item(0)->getAttribute('code');
            throw new \Exception("CAS Authentication Failed: {$reason}");
        }

        return false;
    }

    /**
     * Use a ticket to authenticate a user and get a userId
     *
     * @param string $service
     * @param string $ticket
     *
     * @return string $userId
     */
    protected function getUrl($service, $ticket)
    {
        $validate = '';
        switch ($this->casVersion) {
            case 1:
                $validate = 'validate';
                break;
            case 2:
                $validate = 'serviceValidate';
                break;
            case 3:
                $validate = 'p3/serviceValidate';
                break;
        }
        $url = $this->casServer . '/' .
            $validate .
            '?service=' . $service .
            '&ticket=' . $ticket;

        return $url;
    }

    /**
     * Get the XML response from the CAS server
     *
     * @param string $url
     *
     * @return \DOMElement
     *
     * @throws \Exception
     */
    protected function connect($url)
    {
        $ch = curl_init($url);

        if ($this->casVerifySSL) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

            if ($this->casCertificatePath) {
                curl_setopt($ch, CURLOPT_CAINFO, $this->casCertificatePath);
            }
        } else {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        // return the CURL output into a variable
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        if ($response === false) {
            throw new \Exception(
                'CURL error #' . curl_errno($ch) . ': ' . curl_error($ch)
            );
        }
        curl_close($ch);

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->encoding = "utf-8";

        if (!($dom->loadXML($response))) {
            throw new \Exception(
                'Ticket not validated - bad response from server: ' . var_export($response, true)
            );
        }

        if (!($root = $dom->documentElement)) {
            throw new \Exception(
                'Ticket not validated - bad XML: ' . var_export($response, true)
            );
        }
        if ($root->localName != 'serviceResponse') {
            throw new \Exception(
                'Ticket not validated - bad xml:' . var_export($response, true)
            );
        }

        return $root;
    }
}
