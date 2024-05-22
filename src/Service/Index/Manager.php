<?php

declare(strict_types=1);

namespace App\Service\Index;

use App\Classes\OpenSearchBase;
use OpenSearch\Common\Exceptions\Missing404Exception;

class Manager extends OpenSearchBase
{
    public function drop()
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
        $pipelines = [
            Users::getPipeline()['id'],
            LearningMaterials::getPipeline()['id'],
            Curriculum::getPipeline()['id'],
        ];
        foreach ($pipelines as $id) {
            try {
                $this->client->ingest()->deletePipeline(['id' => $id]);
            } catch (Missing404Exception $e) {
                //do nothing, if the pipeline doesn't exist
            }
        }
        foreach (LearningMaterials::getEnrichPolicies() as $policy) {
            try {
                $this->client->enrich()->deletePolicy(['name' => $policy['name']]);
            } catch (Missing404Exception $e) {
                //do nothing, if the policy doesn't exist
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
        $this->client->indices()->create([
            'index' => LearningMaterials::INDEX,
            'body' => LearningMaterials::getMapping(),
        ]);

        foreach (LearningMaterials::getEnrichPolicies() as $policy) {
            $this->client->enrich()->putPolicy($policy);
            $this->client->enrich()->executePolicy(['name' => $policy['name']]);
        }

        $this->client->ingest()->putPipeline(Users::getPipeline());
        $this->client->ingest()->putPipeline(Curriculum::getPipeline());
        $this->client->ingest()->putPipeline(LearningMaterials::getPipeline());
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
