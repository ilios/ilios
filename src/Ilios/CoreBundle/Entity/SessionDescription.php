<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\DescribableEntity;

/**
 * Class SessionDescription
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="session_description")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class SessionDescription implements SessionDescriptionInterface
{
    use DescribableEntity;

    /**
     * @var SessionInterface
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Session", inversedBy="sessionDescription")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="session_id", referencedColumnName="session_id", unique=true)
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $session;

    /**
    * @ORM\Column(name="description", type="text", nullable=true)
    * @var string
    */
    protected $description;

    /**
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @return SessionInterface
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->session;
    }
}
