<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserPreferenceRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\StringableIdEntity;
use App\Attributes as IA;
use Symfony\Component\Validator\Constraints as Assert;
use Override;

#[ORM\Table(name: 'user_preference')]
#[ORM\Entity(repositoryClass: UserPreferenceRepository::class)]
#[IA\Entity]
class UserPreference implements UserPreferenceInterface
{
    use StringableIdEntity;

    #[ORM\Column(type: 'json_object', nullable: false)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Json]
    #[Assert\NotNull]
    #[Assert\Length(min: 1, max: 65000)]
    protected string $json;

    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\Id]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected UserInterface $user;

    #[Override]
    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    #[Override]
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    #[Override]
    public function setJson(string $json): void
    {
        $this->json = $json;
    }

    #[Override]
    public function getJson(): string
    {
        return $this->json;
    }
}
