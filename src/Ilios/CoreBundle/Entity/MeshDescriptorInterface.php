<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;


use Ilios\CoreBundle\Traits\ConceptsEntityInterface;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\NameableEntityInterface;
use Ilios\CoreBundle\Traits\ObjectivesEntityInterface;
use Ilios\CoreBundle\Traits\TimestampableEntityInterface;
use Ilios\CoreBundle\Traits\CoursesEntityInterface;
use Ilios\CoreBundle\Traits\SessionsEntityInterface;

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
