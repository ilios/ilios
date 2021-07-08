<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Repository\IngestionExceptionRepository;

/**
 * Class IngestionException
 * @IS\Entity
 */
#[ORM\Entity(repositoryClass: IngestionExceptionRepository::class)]
#[ORM\Table(name: 'ingestion_exception')]
class IngestionException implements IngestionExceptionInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    /**
     * @var int
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'ingestion_exception_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 32
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'ingested_wide_uid', type: 'string', length: 32)]
    protected $uid;
    /**
     * @var UserInterface
     *      name="user_id",
     *      referencedColumnName="user_id",
     *      onDelete="CASCADE",
     *      unique=true,
     *      nullable=false
     * )})
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\OneToOne(targetEntity: 'User')]
    #[ORM\JoinColumn(
        name: 'user_id',
        referencedColumnName: 'user_id',
        onDelete: 'CASCADE',
        unique: true,
        nullable: false
    )]
    protected $user;
    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }
    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }
    /**
     * {@inheritdoc}
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }
    /**
     * {@inheritdoc}
     */
    public function getUid()
    {
        return $this->uid;
    }
}
