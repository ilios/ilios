<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableStringEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\CompetenciesEntity;
use App\Attributes as IA;
use App\Repository\AamcPcrsRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AamcPcrsRepository::class)]
#[ORM\Table(name: 'aamc_pcrs')]
#[IA\Entity]
class AamcPcrs implements AamcPcrsInterface
{
    use IdentifiableStringEntity;
    use CompetenciesEntity;

    #[ORM\Column(name: 'pcrs_id', type: 'string', length: 21)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 21)]
    protected string $id;

    #[ORM\Column(name: 'description', type: 'text')]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 65000)]
    protected string $description;

    #[ORM\ManyToMany(targetEntity: 'Competency', mappedBy: 'aamcPcrses')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $competencies;

    public function __construct()
    {
        $this->competencies = new ArrayCollection();
    }

    public function addCompetency(CompetencyInterface $competency): void
    {
        if (!$this->competencies->contains($competency)) {
            $this->competencies->add($competency);
            $competency->addAamcPcrs($this);
        }
    }

    public function removeCompetency(CompetencyInterface $competency): void
    {
        if ($this->competencies->contains($competency)) {
            $this->competencies->removeElement($competency);
            $competency->removeAamcPcrs($this);
        }
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function __toString(): string
    {
        return $this->id ?? '';
    }
}
