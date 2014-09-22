<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\AuditAtomManagerInterface;
use Ilios\CoreBundle\Model\AuditAtomInterface;

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
