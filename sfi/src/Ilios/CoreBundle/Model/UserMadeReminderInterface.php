<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\IdentifiableTraitInterface;
use Ilios\CoreBundle\Traits\TimestampableTraitinterface;

/**
 * Interface UserMadeReminderInterface
 */
interface UserMadeReminderInterface extends IdentifiableTraitInterface, TimestampableTraitinterface
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

