<?php

namespace Ilios\CoreBundle\Entity\LearningMaterials;

use Doctrine\ORM\Mapping as ORM;

use Ilios\CoreBundle\Entity\LearningMaterial;

/**
 * Class Link
 * @package Ilios\CoreBundle\Entity\LearningMaterials
 *
 * @ORM\Entity
 */
class Link extends LearningMaterial implements LinkInterface
{
    /**
     * @var string
     *
     * @ORM\Column(name="web_link", type="string", length=256, nullable=true)
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
