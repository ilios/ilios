<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\AuditAtomManagerInterface;
use Ilios\CoreBundle\Entity\AuditAtomInterface;

/**
 * AuditAtomManager
 */
abstract class AuditAtomManager implements AuditAtomManagerInterface
{
    /**
     * @return AuditAtomInterface
     */
     public function createAuditAtom()
     {
         $class = $this->getClass();

         return new $class();
     }
}
