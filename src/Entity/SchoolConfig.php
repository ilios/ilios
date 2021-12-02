<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\IdentifiableEntity;
use App\Traits\NameableEntity;
use App\Traits\StringableIdEntity;
use App\Attribute as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\SchoolEntity;
use App\Repository\SchoolConfigRepository;

/**
 * Class SchoolConfig
 */
#[ORM\Table(name: 'school_config')]
#[ORM\UniqueConstraint(name: 'school_conf_uniq', columns: ['school_id', 'name'])]
#[ORM\Entity(repositoryClass: SchoolConfigRepository::class)]
#[IA\Entity]
class SchoolConfig implements SchoolConfigInterface
{
    use SchoolEntity;
    use NameableEntity;
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @var int
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    protected $id;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 1,
     *      max = 200
     * )
     */
    #[ORM\Column(type: 'string', length: 200, nullable: false)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $name;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     */
    #[ORM\Column(name: 'value', type: 'text', nullable: false)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $value;

    /**
     * @var SchoolInterface
     * @Assert\NotNull()
     */
    #[ORM\ManyToOne(targetEntity: 'School', inversedBy: 'configurations')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $school;

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }
}
