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
    protected $cache;

    protected function buildCache()
    {
        if (!$this->cache) {
            $this->cache = [];

            /** @var ApplicationConfigRepository $repository */
            $repository = $this->getRepository();
            $configs = $repository->getAllValues();

            foreach ($configs as ['name' => $name, 'value' => $value]) {
                $this->cache[$name] = $value;
            }
        }
    }

    public function getValue($name)
    {
        $this->buildCache();
        if (array_key_exists($name, $this->cache)) {
            return $this->cache[$name];
        }

        return null;
    }
}
