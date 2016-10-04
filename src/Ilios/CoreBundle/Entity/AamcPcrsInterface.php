<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Ilios\CoreBundle\Traits\DescribableEntityInterface;
use Ilios\CoreBundle\Traits\NameableEntityInterface;
use Ilios\CoreBundle\Entity\CompetencyInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

/**
 * Interface AamcPcrsInterface
 */
interface AamcPcrsInterface extends
    IdentifiableEntityInterface,
    DescribableEntityInterface,
    StringableEntityInterface,
    LoggableEntityInterface
{
    /**
     * @param Collection $competencies
     */
    public function setCompetencies(Collection $competencies);

    /**
     * @param CompetencyInterface $competency
     */
    public function addCompetency(CompetencyInterface $competency);

    /**
     * @param CompetencyInterface $competency
     */
    public function removeCompetency(CompetencyInterface $competency);

    /**
     * @return ArrayCollection|CompetencyInterface[]
     */
    public function getCompetencies();
}
