<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\CompetenciesEntity;
use App\Annotation as IS;
use App\Repository\AamcPcrsRepository;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\DescribableEntity;
use App\Entity\CompetencyInterface;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;

/**
 * Class AamcPcrs
 * @IS\Entity
 */
#[ORM\Entity(repositoryClass: AamcPcrsRepository::class)]
#[ORM\Table(name: 'aamc_pcrs')]
class AamcPcrs implements AamcPcrsInterface
{
    use IdentifiableEntity;
    use DescribableEntity;
    use StringableIdEntity;
    use CompetenciesEntity;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 21
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'pcrs_id', type: 'string', length: 21)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    protected $id;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'description', type: 'text')]
    protected $description;
    /**
     * @var ArrayCollection|CompetencyInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'Competency', mappedBy: 'aamcPcrses')]
    #[ORM\OrderBy(['id' => 'ASC'])]
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
