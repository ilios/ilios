<?php
namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\ApiBundle\Annotation as IS;
use Ilios\AuthenticationBundle\Classes\SessionUser;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class Authentication
 *
 * @ORM\Table(name="authentication")
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\AuthenticationRepository")
 *
 * @IS\Entity
 */
class Authentication implements AuthenticationInterface
{
    /**
     * @var UserInterface
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="User", inversedBy="authentication")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="person_id", referencedColumnName="user_id", onDelete="CASCADE")
     * })
     *
     * @Assert\NotBlank()
     *
     * @IS\Type("entity")
     * @IS\Expose
    */
    protected $user;

    /**
     * @ORM\Column(name="username", type="string", unique=true, length=100, nullable=true)
     * @var string
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 100
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
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
     * @ORM\Column(name="invalidate_token_issued_before", type="datetime", nullable=true)
     *
     * @Assert\DateTime()
     *
     * @IS\Expose
     * @IS\ReadOnly
     * @IS\Type("dateTime")
     */
    protected $invalidateTokenIssuedBefore;

    /**
     * @inheritdoc
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritdoc
     */
    public function setPasswordSha256($passwordSha256)
    {
        $this->passwordSha256 = $passwordSha256;
    }

    /**
     * @inheritdoc
     */
    public function getPasswordSha256()
    {
        return $this->passwordSha256;
    }

    /**
     * @inheritdoc
     */
    public function setPasswordBcrypt($passwordBcrypt)
    {
        if ($passwordBcrypt) {
            $this->setPasswordSha256(null);
        }
        $this->passwordBcrypt = $passwordBcrypt;
    }

    /**
     * @inheritdoc
     */
    public function getPasswordBcrypt()
    {
        return $this->passwordBcrypt;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        $newPassword = $this->getPasswordBcrypt();
        $legacyPassword = $this->getPasswordSha256();
        return $newPassword ? $newPassword : $legacyPassword;
    }


    /**
     * @inheritdoc
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @inheritdoc
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @inheritdoc
     */
    public function isLegacyAccount()
    {
        return (bool) $this->getPasswordSha256();
    }

    /**
     * @inheritdoc
     */
    public function setInvalidateTokenIssuedBefore(\DateTime $invalidateTokenIssuedBefore = null)
    {
        $this->invalidateTokenIssuedBefore = $invalidateTokenIssuedBefore;
    }

    /**
     * @inheritdoc
     */
    public function getInvalidateTokenIssuedBefore()
    {
        return $this->invalidateTokenIssuedBefore;
    }
    
    /**
    * @inheritdoc
    */
    public function __toString()
    {
        return (string) $this->user;
    }

    /**
     * @inheritDoc
     */
    public function getSessionUser()
    {
        return new SessionUser($this->user);
    }
}
