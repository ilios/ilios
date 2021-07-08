<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\LearningMaterialsEntity;
use App\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\TitledEntity;
use App\Traits\StringableIdEntity;
use App\Repository\LearningMaterialStatusRepository;

/**
 * Class LearningMaterialStatus
 * @IS\Entity
 */
#[ORM\Table(name: 'learning_material_status')]
#[ORM\Entity(repositoryClass: LearningMaterialStatusRepository::class)]
class LearningMaterialStatus implements LearningMaterialStatusInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use StringableIdEntity;
    use LearningMaterialsEntity;

    /**
     * @deprecated To be removed in 3.1, replaced by ID by enabling trait.
     * @var int
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'learning_material_status_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 60
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(type: 'string', length: 60)]
    protected $title;

    /**
     * @var ArrayCollection|LearningMaterialInterface[]
     */
    #[ORM\OneToMany(targetEntity: 'LearningMaterial', mappedBy: 'status')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $learningMaterials;

    public function __construct()
    {
        $this->learningMaterials = new ArrayCollection();
    }
}
