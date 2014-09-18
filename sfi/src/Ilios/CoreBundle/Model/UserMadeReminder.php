<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserMadeReminder
 */
class UserMadeReminder
{
    /**
     * @var integer
     */
    private $userMadeReminderId;

    /**
     * @var string
     */
    private $note;

    /**
     * @var \DateTime
     */
    private $creationDate;

    /**
     * @var \DateTime
     */
    private $dueDate;

    /**
     * @var boolean
     */
    private $closed;

    /**
     * @var \Ilios\CoreBundle\Entity\User
     */
    private $user;


    /**
     * Get userMadeReminderId
     *
     * @return integer 
     */
    public function getUserMadeReminderId()
    {
        return $this->userMadeReminderId;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return UserMadeReminder
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     * @return UserMadeReminder
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime 
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set dueDate
     *
     * @param \DateTime $dueDate
     * @return UserMadeReminder
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * Get dueDate
     *
     * @return \DateTime 
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * Set closed
     *
     * @param boolean $closed
     * @return UserMadeReminder
     */
    public function setClosed($closed)
    {
        $this->closed = $closed;

        return $this;
    }

    /**
     * Get closed
     *
     * @return boolean 
     */
    public function getClosed()
    {
        return $this->closed;
    }

    /**
     * Set user
     *
     * @param \Ilios\CoreBundle\Entity\User $user
     * @return UserMadeReminder
     */
    public function setUser(\Ilios\CoreBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Ilios\CoreBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
