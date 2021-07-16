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
use App\Repository\LearningMaterialStatusRepository;

/**
 * Class LearningMaterialStatus
 */
#[ORM\Table(name: 'learning_material_status')]
#[ORM\Entity(repositoryClass: LearningMaterialStatusRepository::class)]
#[IA\Entity]
class LearningMaterialStatus implements LearningMaterialStatusInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use StringableIdEntity;
    use LearningMaterialsEntity;

    /**
     * @var int
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(name: 'learning_material_status_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\ReadOnly]
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 60
     * )
     */
    #[ORM\Column(type: 'string', length: 60)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $title;

    /**
     * @var ArrayCollection|LearningMaterialInterface[]
     */
    #[ORM\OneToMany(mappedBy: 'status', targetEntity: 'LearningMaterial')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $learningMaterials;

    public function __construct()
    {
        $this->learningMaterials = new ArrayCollection();
    }
}
