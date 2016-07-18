<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\ActivatableEntity;
use Ilios\CoreBundle\Traits\CategorizableEntity;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\SchoolEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\TitledEntity;

/**
 * Class Vocabulary
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="vocabulary",
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="unique_vocabulary_title", columns={"school_id", "title"})
 *   }
 * )
 * @ORM\Entity()
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class Vocabulary implements VocabularyInterface
{
    use IdentifiableEntity;
    use SchoolEntity;
    use StringableIdEntity;
    use TitledEntity;
    use CategorizableEntity;
    use ActivatableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="vocabulary_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     * @JMS\SerializedName("id")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true)
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
    protected $title;

    /**
     * @var SchoolInterface
     *
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="School", inversedBy="vocabularies")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="school_id", referencedColumnName="school_id", nullable=false)
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("school")
     */
    protected $school;

    /**
     * @var ArrayCollection|TermInterface[]
     *
     * @ORM\OneToMany(targetEntity="Term", mappedBy="vocabulary")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $terms;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    protected $active;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->terms = new ArrayCollection();
        $this->active = true;
    }
}
