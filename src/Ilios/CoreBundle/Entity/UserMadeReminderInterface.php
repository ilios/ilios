<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

/**
 * Interface UserMadeReminderInterface
 */
interface UserMadeReminderInterface extends
    IdentifiableEntityInterface,
    LoggableEntityInterface
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
     * @param string $dueDate
     */
    public function setDueDate(\DateTime $creationDate = null);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

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
