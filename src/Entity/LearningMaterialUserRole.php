<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Attributes as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\TitledEntity;
use App\Repository\LearningMaterialUserRoleRepository;
use Override;

#[ORM\Table(name: 'learning_material_user_role')]
#[ORM\Entity(repositoryClass: LearningMaterialUserRoleRepository::class)]
#[IA\Entity]
class LearningMaterialUserRole implements LearningMaterialUserRoleInterface
{
    use TitledEntity;

    #[ORM\Column(name: 'learning_material_user_role_id', type: 'integer')]
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

    #[ORM\OneToMany(mappedBy: 'userRole', targetEntity: LearningMaterial::class)]
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

    #[Override]
    public function setLearningMaterials(Collection $learningMaterials): void
    {
        $this->learningMaterials = new ArrayCollection();

        foreach ($learningMaterials as $learningMaterial) {
            $this->addLearningMaterial($learningMaterial);
        }
    }

    #[Override]
    public function addLearningMaterial(LearningMaterialInterface $learningMaterial): void
    {
        if (!$this->learningMaterials->contains($learningMaterial)) {
            $this->learningMaterials->add($learningMaterial);
        }
    }

    #[Override]
    public function removeLearningMaterial(LearningMaterialInterface $learningMaterial): void
    {
        $this->learningMaterials->removeElement($learningMaterial);
    }

    #[Override]
    public function getLearningMaterials(): Collection
    {
        return $this->learningMaterials;
    }

    #[Override]
    public function __toString(): string
    {
        return isset($this->id) ? (string) $this->id : '';
    }
}
