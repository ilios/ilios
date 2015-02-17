<?php

namespace Ilios\LegacyCIBundle\Session;

use Ilios\LegacyCIBundle\Utilities;
use Symfony\Bridge\Monolog\Logger;

/**
 * Extract data from CodeIgner session
 */
class Extractor
{

    /**
     * @var Ilios\LegacyCIBundle\Utilities
     */
    private $utilities;

    /**
     * @var Symfony\Bridge\Monolog\Logger
     */
    private $logger;

    /**
     * @var string
     */
    private $sessionCookieName;

    /**
     * @var boolean
     */
    private $isEncrypted;

    /**
     * @var string
     */
    private $encryptionKey;

    /**
     * Constructor
     * @param string $sessionCookieName
     * @param string $encryptionKey
     * @param \Ilios\LegacyCIBundle\Utilities $utilities
     */
    public function __construct(
        $sessionCookieName,
        $encryptionKey,
        $isEncrypted,
        Utilities $utilities,
        Logger $logger
    ) {
        $this->sessionCookieName = $sessionCookieName;
        $this->encryptionKey = $encryptionKey;
        $this->isEncrypted = $isEncrypted;
        $this->utilities = $utilities;
        $this->logger = $logger;
    }

    /**
     * @return bool
     */
    public function getSessionId()
    {
        return $this->get('session_id');
    }

    /**
     * Retrieves a value from the user session by its given key.
     *
     * @param string $key
     *
     * @return string|boolean The value or false if non was found.
     */
    protected function get($key)
    {
        $sessionData = $this->getData();
        if (array_key_exists($key, $sessionData)) {
            return $sessionData[$key];
        }

        return false;
    }

    /**
     * Gets data from the $_SESSION set by code igniter
     *
     * @see CI_Session::sess_read()
     *
     * @return array
     */
    private function getData()
    {
        if (!$string = $this->getCookieString() or
            !$this->validateCookieString($string) or
            !$arr = $this->getCookieArray($string) or
            !$this->validateCookieArray($arr)
        ) {
            return array();
        }

        return $arr;
    }

    private function getCookieString()
    {
        $cookieString = $this->utilities->getCookieData($this->sessionCookieName);
        if (!$cookieString or empty($cookieString)) {
            return false;
        }

        if (strlen($cookieString) <= 40) {
            $this->logger->error('Session: The Code Igniter session cookie was not signed.');
            return false;
        }

        return $cookieString;
    }

    private function validateCookieString($string)
    {
        if (!$this->utilities->validateHash($this->encryptionKey, $string)) {
            $this->logger->error('Session: HMAC mismatch. The session cookie data did not match what was expected.');
            return false;
        }

        return true;
    }

    private function getCookieArray($cookieString)
    {
        $string = substr($cookieString, 0, strlen($cookieString) - 40);
        if ($this->isEncrypted) {
            $string = $this->utilities->decrypt($string, $this->encryptionKey);
        }
        // Unserialize the session array
        $cookieArray = $this->utilities->unserialize($string);
        if (!is_array($cookieArray)) {
            $this->logger->error('CI Session was not extracted into an array.');
            return false;
        }

        return $cookieArray;
    }

    private function validateCookieArray(array $cookieArray)
    {
        $requiredKeys = array(
            'session_id', 'ip_address', 'user_agent', 'last_activity'
        );
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $cookieArray)) {
                $this->logger->error('CI Session was missing key: ' . $key);
                return false;
            }
        }

        if ($userAgent = $this->utilities->getUserAgent()) {
            $userAgent = substr($userAgent, 0, 120);
            // Does the User Agent Match?
            if (trim($cookieArray['user_agent']) !== trim($userAgent)) {
                $this->logger->info(
                    "Mismatched user agents in CI Session ({$userAgent}) vs " .
                    "cookie({$cookieArray['user_agent']})"
                );
                return false;
            }
        }

        return true;
    }
}
