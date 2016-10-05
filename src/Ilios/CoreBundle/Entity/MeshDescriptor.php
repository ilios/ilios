<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\ConceptsEntity;
use Ilios\CoreBundle\Traits\ObjectivesEntity;
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
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\MeshDescriptorRepository")
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
    use ObjectivesEntity;
    use ConceptsEntity;

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
        $this->trees = new ArrayCollection();
        $this->concepts = new ArrayCollection();
        $this->qualifiers = new ArrayCollection();
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
     * @inheritdoc
     */
    public function addObjective(ObjectiveInterface $objective)
    {
        if (!$this->objectives->contains($objective)) {
            $this->objectives->add($objective);
            $objective->addMeshDescriptor($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeObjective(ObjectiveInterface $objective)
    {
        if ($this->objectives->contains($objective)) {
            $this->objectives->removeElement($objective);
            $objective->removeMeshDescriptor($this);
        }
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
        if (!$this->sessionLearningMaterials->contains($sessionLearningMaterial)) {
            $this->sessionLearningMaterials->add($sessionLearningMaterial);
            $sessionLearningMaterial->addMeshDescriptor($this);
        }
    }

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     */
    public function removeSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial)
    {
        if ($this->sessionLearningMaterials->contains($sessionLearningMaterial)) {
            $this->sessionLearningMaterials->removeElement($sessionLearningMaterial);
            $sessionLearningMaterial->removeMeshDescriptor($this);
        }
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
        $this->courseLearningMaterials = new ArrayCollection();

        foreach ($courseLearningMaterials as $courseLearningMaterial) {
            $this->addCourseLearningMaterial($courseLearningMaterial);
        }
    }

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     */
    public function addCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial)
    {
        if (!$this->courseLearningMaterials->contains($courseLearningMaterial)) {
            $this->courseLearningMaterials->add($courseLearningMaterial);
            $courseLearningMaterial->addMeshDescriptor($this);
        }
    }

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     */
    public function removeCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial)
    {
        if ($this->courseLearningMaterials->contains($courseLearningMaterial)) {
            $this->courseLearningMaterials->removeElement($courseLearningMaterial);
            $courseLearningMaterial->removeMeshDescriptor($this);
        }
    }

    /**
     * @return ArrayCollection|CourseLearningMaterialInterface[]
     */
    public function getCourseLearningMaterials()
    {
        return $this->courseLearningMaterials;
    }

    /**
     * @param Collection $qualifiers
     */
    public function setQualifiers(Collection $qualifiers)
    {
        $this->qualifiers = new ArrayCollection();

        foreach ($qualifiers as $qualifier) {
            $this->addQualifier($qualifier);
        }
    }

    /**
     * @param MeshQualifierInterface $qualifier
     */
    public function addQualifier(MeshQualifierInterface $qualifier)
    {
        if (!$this->qualifiers->contains($qualifier)) {
            $this->qualifiers->add($qualifier);
            $qualifier->addDescriptor($this);
        }
    }

    /**
     * @param MeshQualifierInterface $qualifier
     */
    public function removeQualifier(MeshQualifierInterface $qualifier)
    {
        if ($this->qualifiers->contains($qualifier)) {
            $this->qualifiers->removeElement($qualifier);
            $qualifier->removeDescriptor($this);
        }
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
        $this->trees = new ArrayCollection();

        foreach ($trees as $tree) {
            $this->addTree($tree);
        }
    }

    /**
     * @param MeshTreeInterface $tree
     */
    public function addTree(MeshTreeInterface $tree)
    {
        if (!$this->trees->contains($tree)) {
            $this->trees->add($tree);
        }
    }

    /**
     * @param MeshTreeInterface $tree
     */
    public function removeTree(MeshTreeInterface $tree)
    {
        if ($this->trees->contains($tree)) {
            $this->trees->removeElement($tree);
        }
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

    /**
     * @inheritdoc
     */
    public function addCourse(CourseInterface $course)
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->addMeshDescriptor($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeCourse(CourseInterface $course)
    {
        if ($this->courses->contains($course)) {
            $this->courses->removeElement($course);
            $course->removeMeshDescriptor($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function addSession(SessionInterface $session)
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->addMeshDescriptor($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeSession(SessionInterface $session)
    {
        if ($this->sessions->contains($session)) {
            $this->sessions->removeElement($session);
            $session->removeMeshDescriptor($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function addConcept(MeshConceptInterface $concept)
    {
        if (!$this->concepts->contains($concept)) {
            $this->concepts->add($concept);
            $concept->addDescriptor($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeConcept(MeshConceptInterface $concept)
    {
        if ($this->concepts->contains($concept)) {
            $this->concepts->removeElement($concept);
            $concept->removeDescriptor($this);
        }
    }
}
