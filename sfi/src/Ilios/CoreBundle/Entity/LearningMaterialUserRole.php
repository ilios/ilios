<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LearningMaterialUserRole
 */
class LearningMaterialUserRole
{
    /**
     * @var integer
     */
    private $learningMaterialUserRoleId;

    /**
     * @var string
     */
    private $title;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $learningMaterials;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->learningMaterials = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get learningMaterialUserRoleId
     *
     * @return integer 
     */
    public function getLearningMaterialUserRoleId()
    {
        return $this->learningMaterialUserRoleId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return LearningMaterialUserRole
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add learningMaterial
     *
     * @param \Ilios\CoreBundle\Entity\LearningMaterial $learningMaterial
     * @return LearningMaterialUserRole
     */
    public function addLearningMaterial(\Ilios\CoreBundle\Entity\LearningMaterial $learningMaterial)
    {
        $this->learningMaterials[] = $learningMaterial;

        return $this;
    }

    /**
     * Remove learningMaterial
     *
     * @param \Ilios\CoreBundle\Entity\LearningMaterial $learningMaterial
     */
    public function removeLearningMaterial(\Ilios\CoreBundle\Entity\LearningMaterial $learningMaterial)
    {
        $this->learningMaterials->removeElement($learningMaterial);
    }

    /**
     * Get learningMaterials
     *
     * @return \Ilios\CoreBundle\Entity\LearningMaterial[]
     */
    public function getLearningMaterials()
    {
        return $this->learningMaterials->toArray();
    }
}
