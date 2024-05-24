<?php

declare(strict_types=1);

namespace App\Entity;

use App\Attributes as IA;
use App\Traits\ActivatableEntity;
use App\Traits\CategorizableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\MeshDescriptorsEntity;
use App\Traits\SessionConsolidationEntity;
use App\Traits\SortableEntity;
use App\Traits\StringableIdEntity;
use App\Traits\TitledEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\SessionObjectiveRepository;

#[ORM\Table(name: 'session_x_objective')]
#[ORM\Index(columns: ['session_id'], name: 'IDX_FA74B40B613FECDF')]
#[ORM\Entity(repositoryClass: SessionObjectiveRepository::class)]
#[IA\Entity]
class SessionObjective implements SessionObjectiveInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use SessionConsolidationEntity;
    use TitledEntity;
    use MeshDescriptorsEntity;
    use ActivatableEntity;
    use CategorizableEntity;
    use SortableEntity;

    #[ORM\Column(name: 'session_objective_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\ManyToOne(targetEntity: 'Session', inversedBy: 'sessionObjectives')]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'session_id', onDelete: 'CASCADE')]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected SessionInterface $session;

    #[ORM\Column(name: 'position', type: 'integer')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    protected int $position;

    #[ORM\ManyToMany(targetEntity: 'Term', inversedBy: 'sessionObjectives')]
    #[ORM\JoinTable(name: 'session_objective_x_term')]
    #[ORM\JoinColumn(name: 'session_objective_id', referencedColumnName: 'session_objective_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'term_id', referencedColumnName: 'term_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $terms;

    #[ORM\Column(type: 'text')]
    #[IA\Expose]
    #[IA\Type('string')]
    #[IA\RemoveMarkup]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 65000)]
    protected string $title;

    #[ORM\ManyToMany(targetEntity: 'CourseObjective', inversedBy: 'sessionObjectives')]
    #[ORM\JoinTable('session_objective_x_course_objective')]
    #[ORM\JoinColumn(name: 'session_objective_id', referencedColumnName: 'session_objective_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(
        name: 'course_objective_id',
        referencedColumnName: 'course_objective_id',
        onDelete: 'CASCADE'
    )]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $courseObjectives;

    #[ORM\ManyToMany(targetEntity: 'MeshDescriptor', inversedBy: 'sessionObjectives')]
    #[ORM\JoinTable(name: 'session_objective_x_mesh')]
    #[ORM\JoinColumn(name: 'session_objective_id', referencedColumnName: 'session_objective_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(
        name: 'mesh_descriptor_uid',
        referencedColumnName: 'mesh_descriptor_uid',
        onDelete: 'CASCADE'
    )]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $meshDescriptors;

    #[ORM\ManyToOne(targetEntity: 'SessionObjective', inversedBy: 'descendants')]
    #[ORM\JoinColumn(name: 'ancestor_id', referencedColumnName: 'session_objective_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?SessionObjectiveInterface $ancestor = null;

    #[ORM\OneToMany(mappedBy: 'ancestor', targetEntity: 'SessionObjective')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $descendants;

    #[ORM\Column(type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected bool $active;

    public function __construct()
    {
        $this->position = 0;
        $this->active = true;
        $this->terms = new ArrayCollection();
        $this->courseObjectives = new ArrayCollection();
        $this->meshDescriptors = new ArrayCollection();
        $this->descendants = new ArrayCollection();
    }

    public function setSession(SessionInterface $session): void
    {
        $this->session = $session;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function getIndexableCourses(): array
    {
        return [$this->session->getCourse()];
    }

    public function setCourseObjectives(Collection $courseObjectives): void
    {
        $this->courseObjectives = new ArrayCollection();

        foreach ($courseObjectives as $courseObjective) {
            $this->addCourseObjective($courseObjective);
        }
    }

    public function addCourseObjective(CourseObjectiveInterface $courseObjective): void
    {
        if (!$this->courseObjectives->contains($courseObjective)) {
            $this->courseObjectives->add($courseObjective);
        }
    }

    public function removeCourseObjective(CourseObjectiveInterface $courseObjective): void
    {
        $this->courseObjectives->removeElement($courseObjective);
    }

    public function getCourseObjectives(): Collection
    {
        return $this->courseObjectives;
    }

    public function setAncestor(?SessionObjectiveInterface $ancestor = null): void
    {
        $this->ancestor = $ancestor;
    }

    public function getAncestor(): ?SessionObjectiveInterface
    {
        return $this->ancestor;
    }

    public function getAncestorOrSelf(): SessionObjectiveInterface
    {
        $ancestor = $this->getAncestor();

        return $ancestor ?: $this;
    }

    public function setDescendants(Collection $descendants): void
    {
        $this->descendants = new ArrayCollection();

        foreach ($descendants as $descendant) {
            $this->addDescendant($descendant);
        }
    }

    public function addDescendant(SessionObjectiveInterface $descendant): void
    {
        if (!$this->descendants->contains($descendant)) {
            $this->descendants->add($descendant);
        }
    }

    public function removeDescendant(SessionObjectiveInterface $descendant): void
    {
        $this->descendants->removeElement($descendant);
    }

    public function getDescendants(): Collection
    {
        return $this->descendants;
    }

    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function setActive($active): void
    {
        $this->active = $active;
    }

    public function setMeshDescriptors(Collection $meshDescriptors): void
    {
        $this->meshDescriptors = new ArrayCollection();

        foreach ($meshDescriptors as $meshDescriptor) {
            $this->addMeshDescriptor($meshDescriptor);
        }
    }

    public function addMeshDescriptor(MeshDescriptorInterface $meshDescriptor): void
    {
        if (!$this->meshDescriptors->contains($meshDescriptor)) {
            $this->meshDescriptors->add($meshDescriptor);
        }
    }

    public function removeMeshDescriptor(MeshDescriptorInterface $meshDescriptor): void
    {
        $this->meshDescriptors->removeElement($meshDescriptor);
    }
}
