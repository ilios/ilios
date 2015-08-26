<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\NameableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\TimestampableEntity;
use Ilios\CoreBundle\Traits\CoursesEntity;
use Ilios\CoreBundle\Traits\SessionsEntity;

/**
 * Class MeshDescriptor
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="mesh_descriptor")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class MeshDescriptor implements MeshDescriptorInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use NameableEntity;
    use TimestampableEntity;
    use CoursesEntity;
    use SessionsEntity;

    /**
     * @var string
     *
     * @ORM\Column(name="mesh_descriptor_uid", type="string", length=9)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 9
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("id")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=192)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 192
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="annotation", type="text", nullable=true)
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
    protected $annotation;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     *
     * @JMS\Expose
     * @JMS\ReadOnly
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("createdAt")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     *
     * @JMS\Expose
     * @JMS\ReadOnly
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("updatedAt")
     */
    protected $updatedAt;

    /**
     * @var ArrayCollection|CourseInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Course", mappedBy="meshDescriptors")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $courses;

    /**
     * @var ArrayCollection|ObjectiveInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Objective", mappedBy="meshDescriptors")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $objectives;

    /**
     * @var ArrayCollection|SessionInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Session", mappedBy="meshDescriptors")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $sessions;

    /**
     * @var ArrayCollection|MeshConceptInterface[]
     *
     * @ORM\ManyToMany(targetEntity="MeshConcept", mappedBy="descriptors")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $concepts;

    /**
     * @var ArrayCollection|MeshQualifierInterface[]
     *
     * @ORM\ManyToMany(targetEntity="MeshQualifier", mappedBy="descriptors")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $qualifiers;

    /**
     * @var ArrayCollection|MeshTreeInterface[]
     *
     * @ORM\OneToMany(targetEntity="MeshTree", mappedBy="descriptor")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $trees;

    /**
     * @var ArrayCollection|SessionLearningMaterialInterface[]
     *
     * @ORM\ManyToMany(targetEntity="SessionLearningMaterial", mappedBy="meshDescriptors")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("sessionLearningMaterials")
     */
    protected $sessionLearningMaterials;

    /**
     * @var ArrayCollection|CourseLearningMaterialInterface[]
     *
     * @ORM\ManyToMany(targetEntity="CourseLearningMaterial", mappedBy="meshDescriptors")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("courseLearningMaterials")
     */
    protected $courseLearningMaterials;

    /**
     * @var MeshPreviousIndexingInterface
     *
     * @ORM\OneToOne(targetEntity="MeshPreviousIndexing", mappedBy="descriptor")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("previousIndexing")
     */
    protected $previousIndexing;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->courses = new ArrayCollection();
        $this->objectives = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->sessionLearningMaterials = new ArrayCollection();
        $this->courseLearningMaterials = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @param string $annotation
     */
    public function setAnnotation($annotation)
    {
        $this->annotation = $annotation;
    }

    /**
     * @return string
     */
    public function getAnnotation()
    {
        return $this->annotation;
    }

    /**
     * @param Collection $objectives
     */
    public function setObjectives(Collection $objectives)
    {
        $this->objectives = new ArrayCollection();

        foreach ($objectives as $objective) {
            $this->addObjective($objective);
        }
    }

    /**
     * @param ObjectiveInterface $objective
     */
    public function addObjective(ObjectiveInterface $objective)
    {
        $this->objectives->add($objective);
    }

    /**
     * @return ArrayCollection|ObjectiveInterface[]
     */
    public function getObjectives()
    {
        return $this->objectives;
    }

    /**
     * @param Collection $sessionLearningMaterials
     */
    public function setSessionLearningMaterials(Collection $sessionLearningMaterials)
    {
        $this->sessionLearningMaterials = new ArrayCollection();

        foreach ($sessionLearningMaterials as $sessionLearningMaterial) {
            $this->addSessionLearningMaterial($sessionLearningMaterial);
        }
    }

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     */
    public function addSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial)
    {
        $this->sessionLearningMaterials->add($sessionLearningMaterial);
    }

    /**
     * @return ArrayCollection|SessionLearningMaterialInterface[]
     */
    public function getSessionLearningMaterials()
    {
        return $this->sessionLearningMaterials;
    }

    /**
     * @param Collection $courseLearningMaterials
     */
    public function setCourseLearningMaterials(Collection $courseLearningMaterials)
    {
        $this->courseLearningMaterials = $courseLearningMaterials;

        foreach ($courseLearningMaterials as $courseLearningMaterial) {
            $this->addCourseLearningMaterial($courseLearningMaterial);
        }
    }

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     */
    public function addCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial)
    {
        $this->courseLearningMaterials->add($courseLearningMaterial);
    }

    /**
     * @return ArrayCollection|CourseLearningMaterialInterface[]
     */
    public function getCourseLearningMaterials()
    {
        return $this->courseLearningMaterials;
    }

    /**
     * @param Collection $concepts
     */
    public function setConcepts(Collection $concepts)
    {
        $this->concepts = $concepts;

        foreach ($concepts as $concept) {
            $this->addConcept($concept);
        }
    }

    /**
     * @param MeshConceptInterface $concept
     */
    public function addConcept(MeshConceptInterface $concept)
    {
        $this->concepts->add($concept);
    }

    /**
     * @return ArrayCollection|MeshConceptInterface[]
     */
    public function getConcepts()
    {
        return $this->concepts;
    }

    /**
     * @param Collection $qualifiers
     */
    public function setQualifiers(Collection $qualifiers)
    {
        $this->qualifiers = $qualifiers;

        foreach ($qualifiers as $qualifier) {
            $this->addQualifier($qualifier);
        }
    }

    /**
     * @param MeshQualifierInterface $qualifier
     */
    public function addQualifier(MeshQualifierInterface $qualifier)
    {
        $this->qualifiers->add($qualifier);
    }

    /**
     * @return ArrayCollection|MeshQualifierInterface[]
     */
    public function getQualifiers()
    {
        return $this->qualifiers;
    }

    /**
     * @param Collection $trees
     */
    public function setTrees(Collection $trees)
    {
        $this->trees = $trees;

        foreach ($trees as $tree) {
            $this->addTree($tree);
        }
    }

    /**
     * @param MeshTreeInterface $tree
     */
    public function addTree(MeshTreeInterface $tree)
    {
        $this->trees->add($tree);
    }

    /**
     * @return ArrayCollection|MeshTreeInterface[]
     */
    public function getTrees()
    {
        return $this->trees;
    }

    /**
     * @param MeshPreviousIndexingInterface $previousIndexing
     */
    public function setPreviousIndexing(MeshPreviousIndexingInterface $previousIndexing = null)
    {
        $this->previousIndexing = $previousIndexing;
    }

    /**
     * @return MeshPreviousIndexingInterface
     */
    public function getPreviousIndexing()
    {
        return $this->previousIndexing;
    }
}
