<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\ORM\Mapping as ORM;
use Ilios\ApiBundle\Annotation as IS;

/**
 * Class TitledEntity
 * @todo should also contain the $title property, but Doctrine doesn't read teh length properly
 */
trait TitledEntity
{
    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
