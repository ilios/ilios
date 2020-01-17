<?php

declare(strict_types=1);

namespace App\Service\Index;

use App\Classes\ElasticSearchBase;

class Manager extends ElasticSearchBase
{
    public function drop()
    {
        if (!$this->enabled) {
            return;
        }
        $indexes = [
            self::USER_INDEX,
            self::MESH_INDEX,
            self::LEARNING_MATERIAL_INDEX,
            'ilios-public-curriculum',
            'ilios-public-mesh',
            'ilios-private-users',
        ];
        foreach ($indexes as $index) {
            if ($this->client->indices()->exists(['index' => $index])) {
                $this->client->indices()->delete(['index' => $index]);
            }
        }
    }
}
