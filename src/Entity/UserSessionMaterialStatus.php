<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserSessionMaterialStatusRepository;
use App\Traits\TimestampableEntity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Attribute as IA;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'user_session_material_status')]
#[ORM\Entity(repositoryClass: UserSessionMaterialStatusRepository::class)]
#[IA\Entity]
class UserSessionMaterialStatus implements UserSessionMaterialStatusInterface
{
    use TimestampableEntity;

    #[ORM\Column(name: 'id', type: 'bigint')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    protected string $id; //string because doctrine stores bigint as string for best compatibility

    #[ORM\Column(name: 'status', type: 'integer')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[Assert\NotNull]
    #[Assert\Choice([
        UserSessionMaterialStatusInterface::NONE,
        UserSessionMaterialStatusInterface::STARTED,
        UserSessionMaterialStatusInterface::COMPLETE,
    ])]
    #[Assert\Type(type: 'integer')]
    protected int $status;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'sessionMaterialStatuses')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id', nullable: false, onDelete: 'CASCADE')]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected UserInterface $user;

    #[ORM\ManyToOne(targetEntity: SessionLearningMaterial::class)]
    #[ORM\JoinColumn(
        name: 'session_learning_material_id',
        referencedColumnName: 'session_learning_material_id',
        nullable: false,
        onDelete: 'CASCADE'
    )]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected SessionLearningMaterialInterface $material;

    #[ORM\Column(name: 'last_updated_at', type: 'datetime')]
    #[IA\Expose]
    #[IA\OnlyReadable]
    #[IA\Type('dateTime')]
    #[Assert\NotBlank]
    protected DateTime $updatedAt;

    public function __construct()
    {
        $this->updatedAt = new DateTime();
    }

    /**
     * Cast ID to a string to meet doctrine bigint requirements
     */
    public function setId(int $id)
    {
        $this->id = (string) $id;
    }

    /**
     * Cast the ID to an int as doctrine stores a string
     */
    public function getId(): int
    {
        return (int) $this->id;
    }

    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setMaterial(SessionLearningMaterialInterface $material)
    {
        $this->material = $material;
    }

    public function getMaterial(): SessionLearningMaterialInterface
    {
        return $this->material;
    }

    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function __toString(): string
    {
        return $this->id ?? '';
    }
}
