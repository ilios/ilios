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
            Users::INDEX,
            Mesh::INDEX,
            Curriculum::INDEX,
            LearningMaterials::INDEX,
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
            'index' => Users::INDEX,
            'body' => UserMapping::getBody()
        ]);
        $this->client->indices()->create([
            'index' => Mesh::INDEX,
            'body' => MeshMapping::getBody()
        ]);
        $this->client->indices()->create([
            'index' => Curriculum::INDEX,
            'body' => CurriculumMapping::getBody()
        ]);

        $this->client->ingest()->putPipeline(LearningMaterialMapping::getPipeline());
        $this->client->indices()->create([
            'index' => LearningMaterials::INDEX,
            'body' => LearningMaterialMapping::getBody()
        ]);
    }
}
