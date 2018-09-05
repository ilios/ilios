<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use AppBundle\Traits\LearningMaterialsEntity;
use AppBundle\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;

use AppBundle\Traits\IdentifiableEntity;
use AppBundle\Traits\TitledEntity;
use AppBundle\Traits\StringableIdEntity;

/**
 * Class LearningMaterialStatus
 *
 * @ORM\Table(name="learning_material_status")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\LearningMaterialStatusRepository")
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
     * @ORM\OrderBy({"id" = "ASC"})
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
