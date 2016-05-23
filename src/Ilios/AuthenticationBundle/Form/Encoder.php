<?php

namespace Ilios\AuthenticationBundle\Form;

use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

/**
 * Class Encoder
 * @package Ilios\AuthenticationBundle\Form
 */
class Encoder extends BasePasswordEncoder
{
    /**
     * @var string
     */
    protected $salt;

    /**
     * @param string $salt
     */
    public function __construct($salt)
    {
        $this->salt = $salt;
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
        if ($this->salt) {
            $password .= $this->salt;
        }

        $hash = hash('sha256', $password);

        return $hash === $encoded;
    }
}
