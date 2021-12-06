<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\CompetenciesEntity;
use App\Attribute as IA;
use App\Repository\AamcPcrsRepository;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\DescribableEntity;
use App\Entity\CompetencyInterface;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;

/**
 * Class AamcPcrs
 */
#[ORM\Entity(repositoryClass: AamcPcrsRepository::class)]
#[ORM\Table(name: 'aamc_pcrs')]
#[IA\Entity]
class AamcPcrs implements AamcPcrsInterface
{
    use IdentifiableEntity;
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
     */
    #[ORM\Column(name: 'pcrs_id', type: 'string', length: 21)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     */
    #[ORM\Column(name: 'description', type: 'text')]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $description;

    /**
     * @var ArrayCollection|CompetencyInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'Competency', mappedBy: 'aamcPcrses')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $competencies;

    public function __construct()
    {
        $this->competencies = new ArrayCollection();
    }

    public function addCompetency(CompetencyInterface $competency)
    {
        if (!$this->competencies->contains($competency)) {
            $this->competencies->add($competency);
            $competency->addAamcPcrs($this);
        }
    }

    public function removeCompetency(CompetencyInterface $competency)
    {
        if ($this->competencies->contains($competency)) {
            $this->competencies->removeElement($competency);
            $competency->removeAamcPcrs($this);
        }
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
