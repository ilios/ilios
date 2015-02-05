<?php

namespace Ilios\CoreBundle\Entity\LearningMaterials;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

use Ilios\CoreBundle\Entity\LearningMaterial;

/**
 * Class Link
 * @package Ilios\CoreBundle\Entity\LearningMaterials
 *
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class Link extends LearningMaterial implements LinkInterface
{
    /**
     * @var string
     *
     * @ORM\Column(name="web_link", type="string", length=256, nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("webLink")
     */
    protected $webLink;

    /**
     * @param string $webLink
     */
    public function setWebLink($webLink)
    {
        $this->setType(self::TYPE_LINK);
        $this->webLink = $webLink;
    }

    /**
     * @return string
     */
    public function getWebLink()
    {
        return $this->webLink;
    }
}
