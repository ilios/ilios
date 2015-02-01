<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Ilios\CoreBundle\Traits\DescribableEntityInterface;
use Ilios\CoreBundle\Traits\NameableEntityInterface;
use Ilios\CoreBundle\Entity\CompetencyInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;
use Ilios\CoreBundle\Traits\UniversallyUniqueEntityInterface;

/**
 * Interface AamcPcrsInterface
 */
interface AamcPcrsInterface extends
    UniversallyUniqueEntityInterface,
    DescribableEntityInterface,
    StringableEntityInterface
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
     * @return ArrayCollection|CompetencyInterface[]
     */
    public function getCompetencies();
}

