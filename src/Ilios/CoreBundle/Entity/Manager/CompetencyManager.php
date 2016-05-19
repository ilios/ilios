<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\CompetencyInterface;

/**
 * Class CompetencyManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CompetencyManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findCompetencyBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findCompetenciesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateCompetency(
        CompetencyInterface $competency,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($competency, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteCompetency(
        CompetencyInterface $competency
    ) {
        $this->delete($competency);
    }

    /**
     * @deprecated
     */
    public function createCompetency()
    {
        return $this->create();
    }
}
