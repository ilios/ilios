<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;


use App\Traits\ConceptsEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\NameableEntityInterface;
use App\Traits\ObjectivesEntityInterface;
use App\Traits\TimestampableEntityInterface;
use App\Traits\CoursesEntityInterface;
use App\Traits\SessionsEntityInterface;

/**
 * Interface MeshDescriptorInterface
 */
interface MeshDescriptorInterface extends
    IdentifiableEntityInterface,
    NameableEntityInterface,
    TimestampableEntityInterface,
    CoursesEntityInterface,
    SessionsEntityInterface,
    ObjectivesEntityInterface,
    ConceptsEntityInterface
{
    /**
     * @param string $annotation
     */
    public function setAnnotation($annotation);

    /**
     * @return string
     */
    public function getAnnotation();

    /**
     * @param Collection $sessionLearningMaterials
     */
    public function setSessionLearningMaterials(Collection $sessionLearningMaterials);

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     */
    public function addSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial);

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     */
    public function removeSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial);

    /**
     * @return ArrayCollection|SessionLearningMaterialInterface[]
     */
    public function getSessionLearningMaterials();

    /**
     * @param Collection $courseLearningMaterials
     */
    public function setCourseLearningMaterials(Collection $courseLearningMaterials);

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     */
    public function addCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial);

    /**
     * @param CourseLearningMaterialInterface $courseLearningMaterial
     */
    public function removeCourseLearningMaterial(CourseLearningMaterialInterface $courseLearningMaterial);

    /**
     * @return ArrayCollection|CourseLearningMaterialInterface[]
     */
    public function getCourseLearningMaterials();

    /**
     * @param Collection $qualifiers
     */
    public function setQualifiers(Collection $qualifiers);

    /**
     * @param MeshQualifierInterface $qualifier
     */
    public function addQualifier(MeshQualifierInterface $qualifier);

    /**
     * @param MeshQualifierInterface $qualifier
     */
    public function removeQualifier(MeshQualifierInterface $qualifier);

    /**
     * @return ArrayCollection|MeshQualifierInterface[]
     */
    public function getQualifiers();

    /**
     * @param Collection $trees
     */
    public function setTrees(Collection $trees);

    /**
     * @param MeshTreeInterface $tree
     */
    public function addTree(MeshTreeInterface $tree);

    /**
     * @param MeshTreeInterface $tree
     */
    public function removeTree(MeshTreeInterface $tree);

    /**
     * @return ArrayCollection|MeshTreeInterface[]
     */
    public function getTrees();

    /**
     * @param MeshPreviousIndexingInterface $previousIndexing
     */
    public function setPreviousIndexing(MeshPreviousIndexingInterface $previousIndexing);

    /**
     * @return MeshPreviousIndexingInterface
     */
    public function getPreviousIndexing();

    /**
     * @return boolean
     */
    public function isDeleted();

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted);
}
