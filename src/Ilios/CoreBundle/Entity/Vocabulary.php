<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
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
     * @var boolean
     *
     * @ORM\Column(name="hierarchical", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="boolean")
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    protected $hierarchical;

    /**
     * @var SchoolInterface
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
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $terms;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->terms = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function setHierarchical($hierarchical)
    {
        $this->hierarchical = $hierarchical;
    }

    /**
     * @inheritdoc
     */
    public function isHierarchical()
    {
        return $this->hierarchical;
    }

    /**
     * @inheritdoc
     */
    public function setTerms(Collection $terms)
    {
        $this->terms = new ArrayCollection();

        foreach ($terms as $term) {
            $this->addTerm($term);
        }
    }

    /**
     * @inheritdoc
     */
    public function addTerm(TermInterface $term)
    {
        $this->terms->add($term);
    }

    /**
     * @inheritdoc
     */
    public function getTerms()
    {
        return $this->terms;
    }
}
