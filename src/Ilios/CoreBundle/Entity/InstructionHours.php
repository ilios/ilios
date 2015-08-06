<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\TimestampableEntity;

/**
 * Class InstructionHours
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="instruction_hours")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class InstructionHours implements InstructionHoursInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use TimestampableEntity;

    /**
     * @var integer
     *
     * @ORM\Column(name="instruction_hours_id", type="integer")
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
     * @var \DateTime
     *
     * @ORM\Column(name="generation_time_stamp", type="datetime")
     */
    protected $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="hours_accrued", type="integer")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     *
     */
    protected $hoursAccrued;

    /**
     * @var boolean
     *
     * @ORM\Column(name="modified", type="boolean")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="bool")
     */
    protected $modified;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modification_time_stamp", type="datetime")
     *
     * @Assert\NotBlank()
     */
    protected $updatedAt;

    /**
     * original annotation: ORM\Column(name="user_id", type="integer")
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="instructionHours")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", nullable=false)
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $user;

    /**
     * original annotation: ORM\Column(name="session_id", type="integer")
     * @var SessionInterface
     *
     * @ORM\ManyToOne(targetEntity="Session", inversedBy="instructionHours")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="session_id", referencedColumnName="session_id", nullable=false)
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $session;

    /**
     * @param int $hoursAccrued
     */
    public function setHoursAccrued($hoursAccrued)
    {
        $this->hoursAccrued = $hoursAccrued;
    }

    /**
     * @return int
     */
    public function getHoursAccrued()
    {
        return $this->hoursAccrued;
    }

    /**
     * @param boolean $modified
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
    }

    /**
     * @return boolean
     */
    public function isModified()
    {
        return $this->modified;
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
}
