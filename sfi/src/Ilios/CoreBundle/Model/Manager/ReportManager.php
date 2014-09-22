<?php

namespace Ilios\CoreBundle\Model\Manager;

use Ilios\CoreBundle\Model\Manager\ReportManagerInterface;
use Ilios\CoreBundle\Model\ReportInterface;

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
