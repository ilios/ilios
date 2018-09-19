<?php

namespace App\Entity\Manager;

use App\Entity\Repository\ProgramYearRepository;

/**
 * Class ProgramYearManager
 * @package App\Entity\Manager
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
