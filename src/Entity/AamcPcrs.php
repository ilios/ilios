<?php

declare(strict_types=1);

namespace App\Entity;

use App\Attributes as IA;
use App\Repository\AamcPcrsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AamcPcrsRepository::class)]
#[ORM\Table(name: 'aamc_pcrs')]
#[IA\Entity]
class AamcPcrs implements AamcPcrsInterface
{
    #[ORM\Column(name: 'pcrs_id', type: 'string', length: 21)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 21)]
    protected ?string $id;

    #[ORM\Column(name: 'description', type: 'text')]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 65000)]
    protected string $description;

    #[ORM\ManyToMany(targetEntity: Competency::class, mappedBy: 'aamcPcrses')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $competencies;

    public function __construct()
    {
        $this->competencies = new ArrayCollection();
    }

    #[Override]
    public function addCompetency(CompetencyInterface $competency): void
    {
        if (!$this->competencies->contains($competency)) {
            $this->competencies->add($competency);
            $competency->addAamcPcrs($this);
        }
    }

    #[Override]
    public function removeCompetency(CompetencyInterface $competency): void
    {
        if ($this->competencies->contains($competency)) {
            $this->competencies->removeElement($competency);
            $competency->removeAamcPcrs($this);
        }
    }

    #[Override]
    public function setCompetencies(Collection $competencies): void
    {
        $this->competencies = new ArrayCollection();

        foreach ($competencies as $competency) {
            $this->addCompetency($competency);
        }
    }

    #[Override]
    public function getCompetencies(): Collection
    {
        return $this->competencies;
    }

    #[Override]
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    #[Override]
    public function getDescription(): string
    {
        return $this->description;
    }

    #[Override]
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    #[Override]
    public function getId(): ?string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->id ?? '';
    }
}
