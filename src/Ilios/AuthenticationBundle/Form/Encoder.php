<?php

namespace Ilios\AuthenticationBundle\Form;

use Ilios\CoreBundle\Service\Config;
use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

/**
 * Class Encoder
 */
class Encoder extends BasePasswordEncoder
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @param string $salt
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function encodePassword($raw, $salt)
    {
        throw new \Exception("Do not use this legacy encoder to encode new passwords");
    }

    public function isPasswordValid($encoded, $raw, $salt)
    {
        //we use a global salt not the user one
        $salt = null;
        if ($this->isPasswordTooLong($raw)) {
            return false;
        }
        $password = $raw;
        $legacySalt = $this->config->get('legacy_password_salt');
        if ($legacySalt) {
            $password .= $legacySalt;
        }

        $hash = hash('sha256', $password);

        return $hash === $encoded;
    }
}
