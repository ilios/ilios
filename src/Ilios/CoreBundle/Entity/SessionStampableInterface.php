<?php
namespace Ilios\CoreBundle\Entity;

interface SessionStampableInterface
{
    /**
     * @return ArrayCollection|SessionInterface[]
     */
    public function getSessions();
}
