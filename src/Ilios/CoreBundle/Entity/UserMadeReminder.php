<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserMadeReminder
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="user_made_reminder",
 *   indexes={
 *     @ORM\Index(name="due_closed_user_k", columns={"due_date", "closed", "user_id"})
 *   }
 * )
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class UserMadeReminder implements UserMadeReminderInterface
{
    /**
     * @deprecated To be removed in 3.1, replaced by ID by enabling trait.
     * @var int
     *
     * @ORM\Column(name="user_made_reminder_id", type="integer")
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
     * @var string
     *
     * @ORM\Column(name="note", type="string", length=150)
     */
    protected $note;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_date", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="due_date", type="datetime")
     */
    protected $dueDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="closed", type="boolean")
     */
    protected $closed;

    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="reminders")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $user;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->userMadeReminderId = $id;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return ($this->id === null) ? $this->userMadeReminderId : $this->id;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param \DateTime $dueDate
     */
    public function setDueDate(\DateTime $dueDate)
    {
        $this->dueDate = $dueDate;
    }

    /**
     * @return \DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * @param boolean $closed
     */
    public function setClosed($closed)
    {
        $this->closed = $closed;
    }

    /**
     * @return boolean
     */
    public function isClosed()
    {
        return $this->closed;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
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
}
