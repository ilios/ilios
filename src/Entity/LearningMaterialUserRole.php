<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\LearningMaterialsEntity;
use App\Attribute as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\TitledEntity;
use App\Traits\StringableIdEntity;
use App\Repository\LearningMaterialUserRoleRepository;

/**
 * Class LearningMaterialUserRole
 */
#[ORM\Table(name: 'learning_material_user_role')]
#[ORM\Entity(repositoryClass: LearningMaterialUserRoleRepository::class)]
#[IA\Entity]
class LearningMaterialUserRole implements LearningMaterialUserRoleInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use StringableIdEntity;
    use LearningMaterialsEntity;

    /**
     * @var int
     */
    #[ORM\Column(name: 'learning_material_user_role_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected $id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 60)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 60)]
    protected $title;

    /**
     * @var ArrayCollection|LearningMaterialInterface[]
     */
    #[ORM\OneToMany(targetEntity: 'LearningMaterial', mappedBy: 'userRole')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $learningMaterials;

    public function __construct()
    {
        $this->learningMaterials = new ArrayCollection();
    }
}
