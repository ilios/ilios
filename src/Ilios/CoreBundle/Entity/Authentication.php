<?php
namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class Authentication
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="authentication")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class Authentication implements AuthenticationInterface
{
    /**
    * @var UserInterface
    *
    * @ORM\Id
    * @ORM\OneToOne(targetEntity="User", inversedBy="authentication")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="person_id", referencedColumnName="user_id", unique=true, onDelete="CASCADE")
    * })
    *
    * @Assert\NotBlank()
    */
    protected $user;

    /**
    * @ORM\Column(name="username", type="string", unique=true, length=100)
    * @var string
    *
    * @Assert\NotBlank()
    * @Assert\Type(type="string")
    * @Assert\Length(
    *      min = 1,
    *      max = 100
    * )
    *
    */
    private $username;

    /**
    * @ORM\Column(name="password_sha256", type="string", length=64, nullable=true)
    * @var string
    *
    * @Assert\Type(type="string")
    * @Assert\Length(
    *      min = 1,
    *      max = 64
    * )
    *
    */
    private $passwordSha256;

    /**
    * @ORM\Column(name="password_bcrypt", type="string", nullable=true)
    * @var string
    *
    * @Assert\Type(type="string")
    * @Assert\Length(
    *      min = 1,
    *      max = 64
    * )
    *
    */
    private $passwordBcrypt;

    /**
    * @ORM\Column(name="eppn", type="string", nullable=true, unique=true)
    * @var string
    *
    * @Assert\Type(type="string")
    * @Assert\Length(
    *      min = 1,
    *      max = 250
    * )
    *
    */
    private $eppn;

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $passwordSha256
     */
    public function setPasswordSha256($passwordSha256)
    {
        $this->passwordSha256 = $passwordSha256;
    }

    /**
     * @return string
     */
    public function getPasswordSha256()
    {
        return $this->passwordSha256;
    }

    /**
     * @param string $passwordBcrypt
     */
    public function setPasswordBcrypt($passwordBcrypt)
    {
        if ($passwordBcrypt) {
            $this->setPasswordSha256(null);
        }
        $this->passwordBcrypt = $passwordBcrypt;
    }

    /**
     * @return string
     */
    public function getPasswordBcrypt()
    {
        return $this->passwordBcrypt;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $eppn
     */
    public function setEppn($eppn)
    {
        $this->eppn = $eppn;
    }

    /**
     * @return string
     */
    public function getEppn()
    {
        return $this->eppn;
    }

    /**
     * Check if this is a legacy account
     * @return boolean
     */
    public function isLegacyAccount()
    {
        return (bool) $this->getPasswordSha256();
    }
    
    /**
    * @return string
    */
    public function __toString()
    {
        return (string) $this->user;

    }
}
