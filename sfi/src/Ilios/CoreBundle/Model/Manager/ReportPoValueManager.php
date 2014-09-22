<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\ReportPoValueManagerInterface;
use Ilios\CoreBundle\Model\ReportPoValueInterface;

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
