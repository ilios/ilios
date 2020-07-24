<?php

declare(strict_types=1);

namespace App\Entity\Manager;

use App\Entity\Repository\SessionRepository;

/**
 * Class SessionManager
 */
class SessionManager extends V1CompatibleBaseManager
{
    /**
     * @return int
     */
    public function getTotalSessionCount(): int
    {
        /** @var SessionRepository $repository */
        $repository = $this->getRepository();
        return $repository->getTotalSessionCount();
    }
}
