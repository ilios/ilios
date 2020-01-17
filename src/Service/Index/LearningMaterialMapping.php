<?php

declare(strict_types=1);

namespace App\Service\Index;

class LearningMaterialMapping
{
    public static function getBody(): array
    {
        return [
            'mappings' => [
                '_doc' => [
                    'properties' => [
                        'learningMaterialId' => [
                            'type' => 'integer'
                        ],
                        'material' => [
                            'type' => 'object'
                        ],
                    ]
                ]
            ]
        ];
    }

    public static function getPipeline(): array
    {
        return [
            'id' => 'learning_materials',
            'body' => [
                'description' => 'Learning Material Data',
                'processors' => [
                    [
                        'attachment' => [
                            'field' => 'data',
                            'target_field' => 'material',
                        ]
                    ]
                ]
            ]
        ];
    }
}
