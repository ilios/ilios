<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\DependencyInjection\ContainerAware;

use Ilios\CoreBundle\Traits\IdentifiableEntity;

/**
 * Class CISession
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(
 *  name="ci_sessions",
 *  indexes={
 *   @ORM\Index(name="last_activity_idx", columns={"last_activity"})
 *  }
 * )
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class CISession extends ContainerAware implements CISessionInterface
{
    use IdentifiableEntity;

    /**
    * @deprecated Replace with trait.
    * @var int
    *
    * @ORM\Column(name="session_id", type="string", length=40)
    * @ORM\Id
    *
    * @Assert\Type(type="integer")
    *
    * @JMS\Expose
    * @JMS\Type("integer")
    */
    protected $id;

    /**
    * @var string
    *
    * @ORM\Column(name="ip_address", type="string", length=45)
    */
    private $ipAddress;

    /**
    * @var string
    *
    * @ORM\Column(name="user_agent", type="string", length=120)
    */
    private $userAgent;

    /**
    * @var string
    *
    * @ORM\Column(name="last_activity", type="integer")
    */
    private $lastActivity;

    /**
    * @var string
    *
    * @ORM\Column(name="user_data", type="text")
    */
    private $userData;

    /**
     * @param string $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param string $userAgent
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    /**
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param integer $lastActivity
     */
    public function setLastActivity($lastActivity)
    {
        $this->lastActivity = $lastActivity;
    }

    /**
     * @return integer
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }

    /**
     * @param string $userData
     * @return CISession
     */
    public function setUserData($userData)
    {
        $this->unserializedData = null;
        $utilities = $this->container->get('ilios_legacy.utilities');
        $this->userData = $utilities->serialize($userData);
        return $this;
    }

    /**
     * Get userData
     *
     * @return string
     */
    public function getUserData()
    {
        return $this->getUnserializedUserData();
    }

    /**
     * Retrieves a user data item by its given key.
     *
     * @param string $key
     * @return mixed The user data value, or FALSE if not found.
     */
    public function getUserDataItem($key)
    {
        $data = $this->getUnserializedUserData();
        if (!$data) {
            return false;
        }
        return array_key_exists($key, $data) ? $data[$key] : false;
    }

    /**
     * Get unserialized data
     *
     * @return mixed
     */
    protected function getUnserializedUserData()
    {
        if (!isset($this->unserializedData)) {
            $utilities = $this->container->get('ilios_legacy.utilities');
            $this->unserializedData = $utilities->unserialize($this->userData);
        }

        return $this->unserializedData;
    }
}
