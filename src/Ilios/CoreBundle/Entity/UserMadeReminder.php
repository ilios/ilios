<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\ApiBundle\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class UserMadeReminder
 *
 * @ORM\Table(name="user_made_reminder",
 *   indexes={
 *     @ORM\Index(name="due_closed_user_k", columns={"due_date", "closed", "user_id"})
 *   }
 * )
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\UserMadeReminderRepository")
 *
 * @IS\Entity
 */
class UserMadeReminder implements UserMadeReminderInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="user_made_reminder_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="string", length=150)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 150
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $note;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="creation_date", type="datetime")
     *
     * @Assert\NotBlank()
     *
     * @IS\Expose
     * @IS\ReadOnly
     * @IS\Type("dateTime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="due_date", type="datetime")
     *
     * @Assert\NotBlank()
     *
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    protected $dueDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="closed", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    protected $closed;

    /**
     * @var UserInterface
     *
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="reminders")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", nullable=false)
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->closed = false;
        $this->createdAt = new \DateTime();
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
    public function setDueDate(\DateTime $dueDate = null)
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
