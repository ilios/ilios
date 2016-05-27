<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class PendingUserUpdate
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="pending_user_update")
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\PendingUserUpdateRepository")
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class PendingUserUpdate implements PendingUserUpdateInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="exception_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 32
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=true)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 32
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $property;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 255
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $value;

    /**
     * @var UserInterface
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="pendingUserUpdates")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $user;

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
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
     *
     * @return string
     */
    public function getProperty()
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
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

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
}
