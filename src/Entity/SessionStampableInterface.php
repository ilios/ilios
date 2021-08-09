<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

interface SessionStampableInterface
{
    /**
     * @return ArrayCollection|SessionInterface[]
     */
    public function getSessions();
}
