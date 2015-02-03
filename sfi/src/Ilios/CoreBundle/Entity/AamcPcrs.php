<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Entity\CompetencyInterface;
use Ilios\CoreBundle\Traits\UniversallyUniqueEntity;
use Ilios\CoreBundle\Traits\StringableUuidEntity;

/**
 * Class AamcPcrs
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="aamc_pcrs")
 *
 * @JMS\ExclusionPolicy("all")
 */
class AamcPcrs implements AamcPcrsInterface
{
    use UniversallyUniqueEntity;
    use DescribableEntity;
    use StringableUuidEntity;

    /**
     * @var string
     *
     * @ORM\Column(name="pcrs_id", type="string", length=21)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("id")
     */
    protected $uuid;

    /**
    * @ORM\Column(name="description", type="text")
    * @var string
    */
    protected $description;

    /**
     * @var ArrayCollection|CompetencyInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Competency", mappedBy="aamcPcrses")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
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
