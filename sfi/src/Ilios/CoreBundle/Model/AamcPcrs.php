<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Ilios\CoreBundle\Traits\DescribableTrait;
use Ilios\CoreBundle\Traits\IdentifiableTrait;
use Ilios\CoreBundle\Traits\NameableTrait;
use Ilios\CoreBundle\Model\CompetencyInterface;

class AamcPcrs implements AamcPcrsInterface
{
    use IdentifiableTrait;
    use NameableTrait;
    use DescribableTrait;

    /**
     * @var ArrayCollection|CompetencyInterface[]
     */
    protected $competencies;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->competencies = new ArrayCollection();
    }

    /**
     * @param Collection $competencies
     */
    public function setCompetencies(Collection $competencies)
    {
        $this->competencies = new ArrayCollection();

        foreach ($competencies as $competency) {
            $this->addCompetency($competency);
        }
    }

    /**
     * @param CompetencyInterface $competency
     */
    public function addCompetency(CompetencyInterface $competency)
    {
        $this->competencies->add($competency);
    }

    /**
     * @return ArrayCollection|CompetencyInterface[]
     */
    public function getCompetencies()
    {
        return $this->competencies;
    }
}
