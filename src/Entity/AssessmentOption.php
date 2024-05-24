<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Traits\SessionTypesEntity;
use App\Attributes as IA;
use App\Repository\AssessmentOptionRepository;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\NameableEntity;
use App\Traits\StringableIdEntity;

#[ORM\Table(name: 'assessment_option')]
#[ORM\UniqueConstraint(name: 'name', columns: ['name'])]
#[ORM\Entity(repositoryClass: AssessmentOptionRepository::class)]
#[IA\Entity]
class AssessmentOption implements AssessmentOptionInterface
{
    use IdentifiableEntity;
    use NameableEntity;
    use StringableIdEntity;
    use SessionTypesEntity;

    #[ORM\Id]
    #[ORM\Column(name: 'assessment_option_id', type: 'integer', length: 10)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\Column(type: 'string', length: 20)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 18)]
    protected string $name;

    #[ORM\OneToMany(mappedBy: 'assessmentOption', targetEntity: 'SessionType')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $sessionTypes;

    public function __construct()
    {
        $this->sessionTypes = new ArrayCollection();
    }
}
