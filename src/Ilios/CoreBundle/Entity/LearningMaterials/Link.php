<?php

namespace Ilios\CoreBundle\Entity\LearningMaterials;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank()
     * @Assert\Url()
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $link;

    /**
     * @param string $webLink
     */
    public function setLink($link)
    {
        $this->setType(self::TYPE_LINK);
        $this->link = $link;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }
}
