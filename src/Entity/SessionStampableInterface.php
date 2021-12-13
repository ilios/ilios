<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;

interface SessionStampableInterface
{
    public function getSessions(): Collection;
}
