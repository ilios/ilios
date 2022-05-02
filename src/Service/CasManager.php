<?php

declare(strict_types=1);

namespace App\Service;

use DOMDocument;
use Exception;
use DOMElement;

/**
 * Class CasManager
 */
class CasManager
{
    protected ?string $casServer;
    protected ?int $casVersion;
    protected ?bool $casVerifySSL;
    protected ?string $casCertificatePath;

    public function __construct(Config $config, protected Fetch $fetch)
    {
        $this->casServer = $config->get('cas_authentication_server');
        $this->casVersion = $config->get('cas_authentication_version');
        $this->casVerifySSL = $config->get('cas_authentication_verify_ssl');
        $this->casCertificatePath = $config->get('cas_authentication_certificate_path');
    }

    public function getLoginUrl(): string
    {
        return $this->casServer . '/login';
    }

    public function getLogoutUrl(): string
    {
        return $this->casServer . '/logout';
    }

    /**
     * Use a ticket to authenticate a user and get a username
     */
    public function getUsername(string $service, string $ticket): string
    {
        $url = $this->getValidationUrl($service, $ticket);
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

            throw new Exception("CAS Authentication Failed: {$reason}");
        }

        throw new Exception("CAS Authentication Failed for an unknown reason.");
    }

    /**
     * Construct the validation URL for this version of CAS
     */
    protected function getValidationUrl(string $service, string $ticket): string
    {
        $validate = match ($this->casVersion) {
            1 => 'validate',
            2 => 'serviceValidate',
            3 => 'p3/serviceValidate',
            default => throw new Exception("Unknown CAS version: " . $this->casVersion),
        };
        return $this->casServer . '/' .
            $validate .
            '?service=' . $service .
            '&ticket=' . $ticket;
    }

    /**
     * Get the XML response from the CAS server
     */
    protected function connect(string $url): DOMElement
    {
        $response = $this->fetch->get($url);
        $dom = new DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->encoding = "utf-8";

        if (!($dom->loadXML($response))) {
            throw new Exception(
                'Ticket not validated - bad response from server: ' . var_export($response, true)
            );
        }

        if (!($root = $dom->documentElement)) {
            throw new Exception(
                'Ticket not validated - bad XML: ' . var_export($response, true)
            );
        }
        if ($root->localName != 'serviceResponse') {
            throw new Exception(
                'Ticket not validated - bad xml:' . var_export($response, true)
            );
        }

        return $root;
    }
}
