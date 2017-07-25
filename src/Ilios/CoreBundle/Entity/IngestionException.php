<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\ApiBundle\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class IngestionException
 *
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\IngestionExceptionRepository")
 * @ORM\Table(name="ingestion_exception")
 *
 * @IS\Entity
 */
class IngestionException implements IngestionExceptionInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="ingestion_exception_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    protected $id;
    /**
     * @var string
     *
     * @ORM\Column(name="ingested_wide_uid", type="string", length=32)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 32
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $uid;

    /**
     * @var UserInterface
     *
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(
     *      name="user_id",
     *      referencedColumnName="user_id",
     *      onDelete="CASCADE",
     *      unique=true,
     *      nullable=false
     * )})
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
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
