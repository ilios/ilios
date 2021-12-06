<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Attribute as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Repository\IngestionExceptionRepository;

/**
 * Class IngestionException
 */
#[ORM\Entity(repositoryClass: IngestionExceptionRepository::class)]
#[ORM\Table(name: 'ingestion_exception')]
#[IA\Entity]
class IngestionException implements IngestionExceptionInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @var int
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(name: 'ingestion_exception_id', type: 'integer')]
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
    #[ORM\Column(name: 'ingested_wide_uid', type: 'string', length: 32)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $uid;

    /**
     * @var UserInterface
     *      name="user_id",
     *      referencedColumnName="user_id",
     *      onDelete="CASCADE",
     *      unique=true,
     *      nullable=false
     * )})
     */
    #[ORM\OneToOne(targetEntity: 'User')]
    #[ORM\JoinColumn(
        name: 'user_id',
        referencedColumnName: 'user_id',
        unique: true,
        nullable: false,
        onDelete: 'CASCADE'
    )]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $user;

    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    public function getUid(): string
    {
        return $this->uid;
    }
}
