<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Manager\ReportPoValueManagerInterface;
use Ilios\CoreBundle\Entity\ReportPoValueInterface;

/**
 * ReportPoValueManager
 */
abstract class ReportPoValueManager implements ReportPoValueManagerInterface
{
    /**
     * @return ReportPoValueInterface
     */
     public function createReportPoValue()
     {
         $class = $this->getClass();

         return new $class();
     }
}
