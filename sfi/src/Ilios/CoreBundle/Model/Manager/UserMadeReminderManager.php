<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\UserMadeReminderManagerInterface;
use Ilios\CoreBundle\Entity\UserMadeReminderInterface;

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
