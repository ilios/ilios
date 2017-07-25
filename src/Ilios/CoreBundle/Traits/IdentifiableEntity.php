<?php

namespace Ilios\CoreBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class IdentifiableEntity
 */
trait IdentifiableEntity
{
    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}
