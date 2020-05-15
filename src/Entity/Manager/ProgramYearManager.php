<?php

declare(strict_types=1);

namespace App\Entity\Manager;

use App\Entity\Repository\ProgramYearRepository;

/**
 * Class ProgramYearManager
 * @package App\Entity\Manager
 */
class ProgramYearManager extends V1CompatibleBaseManager
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
