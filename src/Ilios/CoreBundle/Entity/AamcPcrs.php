<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\CompetenciesEntity;
use Ilios\ApiBundle\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Entity\CompetencyInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class AamcPcrs
 *
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\AamcPcrsRepository")
 * @ORM\Table(name="aamc_pcrs")
 *
 * @IS\Entity
 */
class AamcPcrs implements AamcPcrsInterface
{
    use IdentifiableEntity;
    use DescribableEntity;
    use StringableIdEntity;
    use CompetenciesEntity;

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
     * @IS\Expose
     * @IS\Type("string")
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
    *
    * @IS\Expose
    * @IS\Type("string")
    */
    protected $description;

    /**
     * @var ArrayCollection|CompetencyInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Competency", mappedBy="aamcPcrses")
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
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
     * @inheritdoc
     */
    public function addCompetency(CompetencyInterface $competency)
    {
        if (!$this->competencies->contains($competency)) {
            $this->competencies->add($competency);
            $competency->addAamcPcrs($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeCompetency(CompetencyInterface $competency)
    {
        if ($this->competencies->contains($competency)) {
            $this->competencies->removeElement($competency);
            $competency->removeAamcPcrs($this);
        }
    }
}
