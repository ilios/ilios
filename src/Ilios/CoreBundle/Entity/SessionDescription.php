<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;

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
    use IdentifiableEntity;
    use DescribableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="description_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
     * @var SessionInterface
     *
     * @ORM\OneToOne(targetEntity="Session", inversedBy="sessionDescription")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="session_id", referencedColumnName="session_id", unique=true, nullable=false)
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $session;

    /**
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @var string
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     *
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
     * @JMS\VirtualProperty
     * @JMS\SerializedName("id")
     *
     * @return string
     */
    public function getId()
    {
        return $this->session->getId();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->session;
    }
}
