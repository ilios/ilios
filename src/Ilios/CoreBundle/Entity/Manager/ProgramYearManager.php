<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Repository\ProgramYearRepository;

/**
 * Class ProgramYearManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class ProgramYearManager extends BaseManager
{
    /**
     * @param int $programYearId
     * @return array
     * @throws \Exception
     */
    public function getProgramYearObjectiveToCourseObjectivesMapping($programYearId): array
    {
        /** @var ProgramYearRepository $repo */
        $repo = $this->getRepository();
        return $repo->getProgramYearObjectiveToCourseObjectivesMapping($programYearId);
    }
}
