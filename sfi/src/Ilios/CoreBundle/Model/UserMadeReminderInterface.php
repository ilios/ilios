<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\TimestampableEntityinterface;

/**
 * Interface UserMadeReminderInterface
 */
interface UserMadeReminderInterface extends IdentifiableEntityInterface, TimestampableEntityinterface
{
    /**
     * @param string $note
     */
    public function setNote($note);

    /**
     * @return string
     */
    public function getNote();

    /**
     * @param \DateTime $dueDate
     */
    public function setDueDate(\DateTime $dueDate);

    /**
     * @return \DateTime
     */
    public function getDueDate();

    /**
     * @param boolean $closed
     */
    public function setClosed($closed);

    /**
     * @return boolean
     */
    public function isClosed();

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);

    /**
     * @return UserInterface
     */
    public function getUser();
}

