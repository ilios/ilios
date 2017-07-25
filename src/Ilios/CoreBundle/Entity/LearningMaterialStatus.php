<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\LearningMaterialsEntity;
use Ilios\ApiBundle\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class LearningMaterialStatus
 *
 * @ORM\Table(name="learning_material_status")
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\LearningMaterialStatusRepository")
 *
 * @IS\Entity
 */
class LearningMaterialStatus implements LearningMaterialStatusInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use StringableIdEntity;
    use LearningMaterialsEntity;

    /**
     * @deprecated To be removed in 3.1, replaced by ID by enabling trait.
     * @var int
     *
     * @ORM\Column(name="learning_material_status_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=60)
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 60
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
    */
    protected $title;

    /**
     * @var ArrayCollection|LearningMaterialInterface[]
     *
     * @ORM\OneToMany(targetEntity="LearningMaterial", mappedBy="status")
     */
    protected $learningMaterials;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->learningMaterials = new ArrayCollection();
    }
}
