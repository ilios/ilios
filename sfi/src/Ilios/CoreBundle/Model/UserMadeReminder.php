<?php

namespace Ilios\CoreBundle\Model;

use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;


/**
 * UserMadeReminder
 */
class UserMadeReminder implements UserMadeReminderInterface
{
    use IdentifiableEntity;
    use TimestampableEntity;

    /**
     * @var string
     */
    protected $note;

    /**
     * @var \DateTime
     */
    protected $dueDate;

    /**
     * @var boolean
     */
    protected $closed;

    /**
     * @var UserInterface
     */
    protected $user;

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
