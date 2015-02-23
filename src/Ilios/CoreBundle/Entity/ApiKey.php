<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class ApiKey
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="api_key",uniqueConstraints={@ORM\UniqueConstraint(name="api_key_api_key", columns={"api_key"})})
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class ApiKey implements ApiKeyInterface
{
    /**
    * @var UserInterface
    *
    * @ORM\Id
    * @ORM\OneToOne(targetEntity="User", inversedBy="apiKey")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", unique=true, onDelete="CASCADE")
    * })
    *
    * @JMS\Expose
    * @JMS\Type("string")
    */
    protected $user;

    /**
    * @ORM\Column(name="api_key", type="string", length=64)
    *
    * @var string
    */
    protected $key;

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user = null)
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
}
