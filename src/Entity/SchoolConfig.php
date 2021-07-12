<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\IdentifiableEntity;
use App\Traits\NameableEntity;
use App\Traits\StringableIdEntity;
use App\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\SchoolEntity;
use App\Repository\SchoolConfigRepository;

/**
 * Class SchoolConfig
 * @IS\Entity
 */
#[ORM\Table(name: 'school_config')]
#[ORM\UniqueConstraint(name: 'school_conf_uniq', columns: ['school_id', 'name'])]
#[ORM\Entity(repositoryClass: SchoolConfigRepository::class)]
class SchoolConfig implements SchoolConfigInterface
{
    use SchoolEntity;
    use NameableEntity;
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @var int
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 200
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(type: 'string', length: 200, nullable: false)]
    protected $name;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'value', type: 'text', nullable: false)]
    protected $value;

    /**
     * @var SchoolInterface
     * @Assert\NotNull()
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'School', inversedBy: 'configurations')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id')]
    protected $school;

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @inheritdoc
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}
