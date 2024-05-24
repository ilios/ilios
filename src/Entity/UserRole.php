<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\UsersEntity;
use App\Attributes as IA;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\TitledEntity;
use App\Traits\StringableIdEntity;
use App\Repository\UserRoleRepository;

#[ORM\Table(name: 'user_role')]
#[ORM\Entity(repositoryClass: UserRoleRepository::class)]
#[IA\Entity]
class UserRole implements UserRoleInterface
{
    use TitledEntity;
    use StringableIdEntity;
    use IdentifiableEntity;
    use UsersEntity;

    #[ORM\Column(name: 'user_role_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\Column(type: 'string', length: 60)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 60)]
    protected string $title;

    /**
     * Don't put users in the UserRole API it takes too long to load
     */
    #[ORM\ManyToMany(targetEntity: 'User', mappedBy: 'roles')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function addUser(UserInterface $user): void
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addRole($this);
        }
    }

    public function removeUser(UserInterface $user): void
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeRole($this);
        }
    }
}
