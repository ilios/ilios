<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Ilios\CoreBundle\Traits\DescribableTraitInterface;
use Ilios\CoreBundle\Traits\IdentifiableTraitInterface;
use Ilios\CoreBundle\Traits\NameableTraitInterface;
use Ilios\CoreBundle\Model\CompetencyInterface;

/**
 * Interface AamcPcrsInterface
 */
interface AamcPcrsInterface extends
    IdentifiableTraitInterface,
    NameableTraitInterface,
    DescribableTraitInterface
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

