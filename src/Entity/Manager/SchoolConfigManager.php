<?php

namespace App\Entity\Manager;

use App\Entity\Repository\SchoolConfigRepository;

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
