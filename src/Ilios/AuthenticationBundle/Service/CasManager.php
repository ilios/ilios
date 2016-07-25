<?php

namespace Ilios\AuthenticationBundle\Service;

/**
 * Class CasManager
 * @package Ilios\AuthenticationBundle\Service
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
     * Constructor
     *
     * @param string $casServer
     * @param string $casVersion
     */
    public function __construct(
        $casServer,
        $casVersion
    ) {
        $this->casServer = $casServer;
        $this->casVersion = $casVersion;
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

        //@todo Setup SSL validation for CURL at this point
        //if this code is still here this is a MAJOR flaw and should
        //be called out or not merged or fixed IMMEDIATLY
        //JRJ is an idiot if you are reading this message.
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

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
