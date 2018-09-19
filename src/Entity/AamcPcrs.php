<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\CompetenciesEntity;
use App\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;

use App\Traits\DescribableEntity;
use App\Entity\CompetencyInterface;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;

/**
 * Class AamcPcrs
 *
 * @ORM\Entity(repositoryClass="App\Entity\Repository\AamcPcrsRepository")
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
     * @ORM\OrderBy({"id" = "ASC"})
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
