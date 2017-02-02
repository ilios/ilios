<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\SchoolEntity;

/**
 * Class SchoolConfig
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="school_config",
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="school_config_unique", columns={"school_id", "name"})
 *   }
 * )
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class SchoolConfig extends ApplicationConfig implements SchoolConfigInterface
{
    use SchoolEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
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
     * @ORM\Column(type="string", length=200, nullable=false)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 200
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", nullable=false)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $value;

    /**
     * @var SchoolInterface
     *
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="School", inversedBy="configurations")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="school_id", referencedColumnName="school_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("school")
     */
    protected $school;
}
