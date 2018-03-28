<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Repository\SchoolConfigRepository;

/**
 * Class SchoolConfigManager
 * @package Ilios\CoreBundle\Entity\Manager
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
