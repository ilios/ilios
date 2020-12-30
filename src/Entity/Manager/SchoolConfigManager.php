<?php

declare(strict_types=1);

namespace App\Entity\Manager;

use App\Repository\SchoolConfigRepository;

/**
 * Class SchoolConfigManager
 * @package App\Entity\Manager
 */
class SchoolConfigManager extends BaseManager
{
    public function getValue($name)
    {
        /** @var SchoolConfigRepository $repository */
        $repository = $this->getRepository();

        return $repository->getValue($name);
    }
}
