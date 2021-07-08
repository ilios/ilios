<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Traits\SessionTypesEntity;
use App\Annotation as IS;
use App\Repository\AssessmentOptionRepository;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\NameableEntity;
use App\Traits\StringableIdEntity;

/**
 * Class AssessmentOption
 * @IS\Entity
 */
#[ORM\Table(name: 'assessment_option')]
#[ORM\UniqueConstraint(name: 'name', columns: ['name'])]
#[ORM\Entity(repositoryClass: AssessmentOptionRepository::class)]
class AssessmentOption implements AssessmentOptionInterface
{
    use IdentifiableEntity;
    use NameableEntity;
    use StringableIdEntity;
    use SessionTypesEntity;
    /**
     * @deprecated To be removed in 3.1, replaced by ID by enabling trait.
     * @var int
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer', length: 10, name: 'assessment_option_id')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 18
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(type: 'string', length: 20)]
    protected $name;
    /**
     * @var ArrayCollection|SessionTypeInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\OneToMany(targetEntity: 'SessionType', mappedBy: 'assessmentOption')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $sessionTypes;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sessionTypes = new ArrayCollection();
    }
}
