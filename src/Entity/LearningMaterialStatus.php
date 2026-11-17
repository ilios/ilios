<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\LearningMaterialsEntity;
use App\Attributes as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\TitledEntity;
use App\Traits\StringableIdEntity;
use App\Repository\LearningMaterialStatusRepository;
use Override;

#[ORM\Table(name: 'learning_material_status')]
#[ORM\Entity(repositoryClass: LearningMaterialStatusRepository::class)]
#[IA\Entity]
class LearningMaterialStatus implements LearningMaterialStatusInterface
{
    use TitledEntity;
    use StringableIdEntity;
    use LearningMaterialsEntity;

    #[ORM\Column(name: 'learning_material_status_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected ?int $id;

    #[ORM\Column(type: 'string', length: 60)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 60)]
    protected string $title;

    #[ORM\OneToMany(mappedBy: 'status', targetEntity: LearningMaterial::class)]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected Collection $learningMaterials;

    public function __construct()
    {
        $this->learningMaterials = new ArrayCollection();
    }
    #[Override]
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    #[Override]
    public function getId(): ?int
    {
        return $this->id;
    }
}
