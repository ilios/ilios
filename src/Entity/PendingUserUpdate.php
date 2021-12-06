<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Attribute as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Repository\PendingUserUpdateRepository;

/**
 * Class PendingUserUpdate
 */
#[ORM\Table(name: 'pending_user_update')]
#[ORM\Entity(repositoryClass: PendingUserUpdateRepository::class)]
#[IA\Entity]
class PendingUserUpdate implements PendingUserUpdateInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @var int
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(name: 'exception_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\ReadOnly]
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 32
     * )
     */
    #[ORM\Column(type: 'string', length: 32)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $type;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 32
     * )
     */
    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $property;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 255
     * )
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $value;

    /**
     * @var UserInterface
     * @Assert\NotNull()
     */
    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'pendingUserUpdates')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $user;

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Set property
     *
     * @param string $property
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }

    /**
     * Get property
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * Set value
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get value
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
