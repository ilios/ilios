<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\UserMadeReminderManagerInterface;
use Ilios\CoreBundle\Model\UserMadeReminderInterface;

/**
 * UserMadeReminderManager
 */
abstract class UserMadeReminderManager implements UserMadeReminderManagerInterface
{
    /**
    * @return UserMadeReminderInterface
    */
    public function createUserMadeReminder()
    {
        $class = $this->getClass();

        return new $class();
    }
}
