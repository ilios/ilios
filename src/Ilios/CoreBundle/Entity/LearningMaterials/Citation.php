<?php

namespace Ilios\CoreBundle\Entity\LearningMaterials;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Entity\LearningMaterial;

/**
 * Class Citation
 * @package Ilios\CoreBundle\Entity\LearningMaterials
 *
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class Citation extends LearningMaterial implements CitationInterface
{
    /**
     * renamed from citation
     * @var string
     *
     * @ORM\Column(name="citation", type="string", length=512, nullable=true)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 512
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $citation;

    /**
     * @param string $text
     */
    public function setCitation($citation)
    {
        $this->setType(self::TYPE_CITATION);
        $this->citation = $citation;
    }

    /**
     * @return string
     */
    public function getCitation()
    {
        return $this->citation;
    }
}
