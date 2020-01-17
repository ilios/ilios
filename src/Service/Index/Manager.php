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
            self::CURRICULUM_INDEX,
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

    public function create()
    {
        if (!$this->enabled) {
            return;
        }

        $this->client->indices()->create([
            'index' => self::USER_INDEX,
            'body' => UserMapping::getBody()
        ]);
        $this->client->indices()->create([
            'index' => self::MESH_INDEX,
            'body' => MeshMapping::getBody()
        ]);
        $this->client->indices()->create([
            'index' => self::CURRICULUM_INDEX,
            'body' => CurriculumMapping::getBody()
        ]);

        $this->client->ingest()->putPipeline(LearningMaterialMapping::getPipeline());
        $this->client->indices()->create([
            'index' => self::LEARNING_MATERIAL_INDEX,
            'body' => LearningMaterialMapping::getBody()
        ]);
    }
}
