<?php

declare(strict_types=1);

namespace App\Entity\Manager;

use App\Entity\Repository\ApplicationConfigRepository;

/**
 * Class ApplicationConfigManager
 * @package App\Entity\Manager
 */
class ApplicationConfigManager extends BaseManager
{
    public function getValue($name)
    {
        /** @var ApplicationConfigRepository $repository */
        $repository = $this->getRepository();

        return $repository->getValue($name);
    }
}
