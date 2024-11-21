<?php

declare(strict_types=1);

namespace App\Service\Index;

use App\Classes\OpenSearchBase;

class Manager extends OpenSearchBase
{
    public function drop(): void
    {
        if (!$this->enabled) {
            return;
        }
        $indexes = [
            ...$this->listCurrentIndexes(),
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

    public function create(): void
    {
        if (!$this->enabled) {
            return;
        }

        $this->client->indices()->create([
            'index' => Users::INDEX,
            'body' => Users::getMapping(),
        ]);
        $this->client->indices()->create([
            'index' => Mesh::INDEX,
            'body' => Mesh::getMapping(),
        ]);
        $this->client->indices()->create([
            'index' => Curriculum::INDEX,
            'body' => Curriculum::getMapping(),
        ]);

        $this->client->ingest()->putPipeline(Curriculum::getPipeline());
        $this->client->ingest()->putPipeline(LearningMaterials::getPipeline());
        $this->client->indices()->create([
            'index' => LearningMaterials::INDEX,
            'body' => LearningMaterials::getMapping(),
        ]);
    }

    public function hasBeenCreated(): bool
    {
        if (!$this->enabled) {
            return false;
        }
        foreach ($this->listCurrentIndexes() as $index) {
            if (!$this->client->indices()->exists(['index' => $index])) {
                return false;
            }
        }

        return true;
    }

    protected function listCurrentIndexes(): array
    {
        return [
            Users::INDEX,
            Mesh::INDEX,
            Curriculum::INDEX,
            LearningMaterials::INDEX,
        ];
    }
}
