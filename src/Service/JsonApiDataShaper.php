<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\JsonApiData;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class JsonApiDataShaper
{
    use NormalizerAwareTrait;

    public function __construct(
        protected EntityRepositoryLookup $entityRepositoryLookup,
        protected AuthorizationCheckerInterface $authorizationChecker,
    ) {
    }

    public function shapeData(array $data, array $sideLoadFields): array
    {
        $jsonApiData = new JsonApiData(
            $this->entityRepositoryLookup,
            $this->normalizer,
            $this->authorizationChecker,
            $data,
            $sideLoadFields
        );
        return $jsonApiData->toArray();
    }

    /**
     * Flattens well structured JSON:API data into the flat array
     * our API has always used.
     */
    public function flattenJsonApiData(object $data): array
    {
        $rhett = [];
        if (property_exists($data, 'id') && property_exists($data, 'type')) {
            $manager = $this->entityRepositoryLookup->getRepositoryForEndpoint($data->type);
            $rhett[$manager->getIdField()] = $data->id;
        }
        foreach ($data->attributes as $key => $value) {
            $rhett[$key] = $value;
        }

        if (property_exists($data, 'relationships')) {
            foreach ($data->relationships as $key => $rel) {
                if (is_array($rel->data)) {
                    $rhett[$key] = [];
                    foreach ($rel->data as $r2) {
                        $rhett[$key][] = $r2->id;
                    }
                } else {
                    $rhett[$key] = is_null($rel->data) ? null : $rel->data->id;
                }
            }
        }

        return $rhett;
    }
}
