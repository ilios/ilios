<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class InstructionHours
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="instruction_hours")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class InstructionHours implements InstructionHoursInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
//    use TimestampableEntity;

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
     * @deprecated To be removed in 3.1, replaced by TimestampableEntity trait.
     * @var \DateTime
     *
     * @ORM\Column(name="generation_time_stamp", type="datetime")
     */
    protected $generationTimeStamp;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="hours_accrued", type="integer")
     */
    protected $hoursAccrued;

    /**
     * @var boolean
     *
     * @ORM\Column(name="modified", type="boolean")
     */
    protected $modified;

    /**
     * @deprecated To be removed in 3.1, replaced by TimestampableEntity trait.
     * @var \DateTime
     *
     * @ORM\Column(name="modification_time_stamp", type="datetime")
     */
    protected $modificationTimeStamp;

    /**
     * @var \DateTime
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
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->generationTimeStamp = $createdAt;
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return ($this->createdAt === null) ? $this->generationTimeStamp : $this->createdAt;
    }

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
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->modificationTimeStamp = $updatedAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAT()
    {
        return ($this->updatedAt === null) ? $this->modificationTimeStamp : $this->updatedAt;
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
