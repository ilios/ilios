<?php

declare(strict_types=1);

namespace App\Service\Index;

class MeshMapping
{
    public static function getBody(): array
    {
        return [
            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
            ],
            'mappings' => [
                '_doc' => [
                    '_meta' => [
                        'version' => '1',
                    ],
                ],
            ],
        ];
    }
}
