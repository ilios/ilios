<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\ReportManagerInterface;
use Ilios\CoreBundle\Entity\ReportInterface;

/**
 * ReportManager
 */
abstract class ReportManager implements ReportManagerInterface
{
    /**
     * @return ReportInterface
     */
     public function createReport()
     {
         $class = $this->getClass();

         return new $class();
     }
}
