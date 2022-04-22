<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\CategorizableEntity;
use App\Traits\DescribableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Traits\TitledEntity;
use App\Attribute as IA;
use App\Repository\AamcResourceTypeRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AamcResourceType
 */
#[ORM\Entity(repositoryClass: AamcResourceTypeRepository::class)]
#[ORM\Table(name: 'aamc_resource_type')]
#[IA\Entity]
class AamcResourceType implements AamcResourceTypeInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use StringableIdEntity;
    use CategorizableEntity;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 21
     * )
     */
    #[ORM\Column(name: 'resource_type_id', type: 'string', length: 21)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 200
     * )
     */
    #[ORM\Column(type: 'string', length: 200)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $title;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     */
    #[ORM\Column(name: 'description', type: 'text')]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $description;

    /**
     * @var ArrayCollection|TermInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'Term', mappedBy: 'aamcResourceTypes')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $terms;

    public function __construct()
    {
        $this->terms = new ArrayCollection();
    }

    public function addTerm(TermInterface $term)
    {
        if (!$this->terms->contains($term)) {
            $this->terms->add($term);
            $term->addAamcResourceType($this);
        }
    }

    public function removeTerm(TermInterface $term)
    {
        if ($this->terms->contains($term)) {
            $this->terms->removeElement($term);
            $term->removeAamcResourceType($this);
        }
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
