<?php

namespace AppBundle\Entity\Manager;

use AppBundle\Entity\Repository\SchoolConfigRepository;

/**
 * Class SchoolConfigManager
 * @package AppBundle\Entity\Manager
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
