<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Entity\CompetencyInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

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
    use IdentifiableEntity;
    use DescribableEntity;
    use StringableIdEntity;

    /**
     * @var string
     *
     * @ORM\Column(name="pcrs_id", type="string", length=21)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 21
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $id;

    /**
    * @ORM\Column(name="description", type="text")
    * @var string
    *
    * @Assert\NotBlank()
    * @Assert\Type(type="string")
    * @Assert\Length(
    *      min = 1,
    *      max = 65000
    * )
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
