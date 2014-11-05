<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Model\CompetencyInterface;
use Ilios\CoreBundle\Traits\UniversallyUniqueEntity;

/**
 * Class AamcPcrs
 * @package Ilios\CoreBundle\Model
 *
 * @ORM\Entity
 * @ORM\Table(name="aamc_pcrs")
 */
class AamcPcrs implements AamcPcrsInterface
{
//    use UniversallyUniqueEntity;
    use DescribableEntity;

    /**
     * @deprecated replace with UniversallyUniqueEntity trait for 3.1.x
     * @var string
     *
     * @ORM\Column(type="string", length=21, name="pcrs_id")
     */
    protected $pcrsId;

    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var ArrayCollection|CompetencyInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Competency")
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
     * @param string $uuid
     */
    public function setUuid($uuid)
    {
        $this->pcrsId = $uuid;
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getUuid()
    {
        return ($this->uuid === null) ? $this->pcrsId : $this->uuid;
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
