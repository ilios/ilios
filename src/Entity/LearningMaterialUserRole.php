<?php

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

/**
 * Class LearningMaterialUserRole
 *
 * @ORM\Table(name="learning_material_user_role")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\LearningMaterialUserRoleRepository")
 *
 * @IS\Entity
 */
class LearningMaterialUserRole implements LearningMaterialUserRoleInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use StringableIdEntity;
    use LearningMaterialsEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="learning_material_user_role_id", type="integer")
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
     * @ORM\OneToMany(targetEntity="LearningMaterial", mappedBy="userRole")
     * @ORM\OrderBy({"id" = "ASC"})
     *
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
