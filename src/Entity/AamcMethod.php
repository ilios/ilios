<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\SessionTypesEntity;
use App\Annotation as IS;
use App\Repository\AamcMethodRepository;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\ActivatableEntity;
use App\Traits\DescribableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;

/**
 * Class AamcMethod
 * @IS\Entity
 */
#[ORM\Table(name: 'aamc_method')]
#[ORM\Entity(repositoryClass: AamcMethodRepository::class)]
class AamcMethod implements AamcMethodInterface
{
    use IdentifiableEntity;
    use DescribableEntity;
    use StringableIdEntity;
    use SessionTypesEntity;
    use ActivatableEntity;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 10
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'method_id', type: 'string', length: 10)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    protected $id;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'description', type: 'text')]
    protected $description;
    /**
     * @var ArrayCollection|SessionTypeInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'SessionType', mappedBy: 'aamcMethods')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $sessionTypes;
    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(type: 'boolean')]
    protected $active;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sessionTypes = new ArrayCollection();
        $this->active = true;
    }
    /**
     * @inheritdoc
     */
    public function addSessionType(SessionTypeInterface $sessionType)
    {
        if (!$this->sessionTypes->contains($sessionType)) {
            $this->sessionTypes->add($sessionType);
            $sessionType->addAamcMethod($this);
        }
    }
    /**
     * @param SessionTypeInterface $sessionType
     */
    public function removeSessionType(SessionTypeInterface $sessionType)
    {
        $this->sessionTypes->removeElement($sessionType);
        $sessionType->removeAamcMethod($this);
    }
}
