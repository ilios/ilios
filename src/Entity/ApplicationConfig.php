<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Annotation as IS;
use App\Repository\ApplicationConfigRepository;
use App\Traits\StringableIdEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\NameableEntity;

/**
 * Class ApplicationConfig
 *   uniqueConstraints={
 *   }
 * )
 * @IS\Entity
 */
#[ORM\Table(name: 'application_config')]
#[ORM\UniqueConstraint(name: 'app_conf_uniq', columns: ['name'])]
#[ORM\Entity(repositoryClass: ApplicationConfigRepository::class)]
class ApplicationConfig implements ApplicationConfigInterface
{
    use IdentifiableEntity;
    use NameableEntity;
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
     * @Assert\NotBlank()
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
     * @Assert\NotBlank()
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
