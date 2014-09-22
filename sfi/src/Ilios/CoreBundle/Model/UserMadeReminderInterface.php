<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface UserMadeReminderInterface
 */
interface UserMadeReminderInterface 
{
    public function getUserMadeReminderId();

    public function setNote($note);

    public function getNote();

    public function setCreationDate($creationDate);

    public function getCreationDate();

    public function setDueDate($dueDate);

    public function getDueDate();

    public function setClosed($closed);

    public function getClosed();

    public function setUser(\Ilios\CoreBundle\Model\User $user = null);

    public function getUser();
}

