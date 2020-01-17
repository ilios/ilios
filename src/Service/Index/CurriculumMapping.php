<?php

declare(strict_types=1);

namespace App\Service\Index;

class CurriculumMapping
{
    public static function getBody(): array
    {
        $txtTypeField = [
            'type' => 'text',
            'analyzer' => 'standard',
            'fields' => [
                'ngram' => [
                    'type' => 'text',
                    'analyzer' => 'ngram_analyzer',
                    'search_analyzer' => 'string_search_analyzer',
                ],
                'english' => [
                    'type' => 'text',
                    'analyzer' => 'english',
                ],
                'raw' => [
                    'type' => 'text',
                    'analyzer' => 'keyword',
                ]
            ],
        ];
        $txtTypeFieldWithCompletion = $txtTypeField;
        $txtTypeFieldWithCompletion['fields']['cmp'] = ['type' => 'completion'];

        return [
            'settings' => [
                'analysis' => self::getAnalyzers(),
                'max_ngram_diff' =>  15,
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
            ],
            'mappings' => [
                '_doc' => [
                    '_meta' => [
                        'version' => '1',
                    ],
                    'properties' => [
                        'courseId' => [
                            'type' => 'keyword',
                        ],
                        'school' => [
                            'type' => 'keyword',
                            'fields' => [
                                'cmp' => [
                                    'type' => 'completion'
                                ]
                            ],
                        ],
                        'courseYear' => [
                            'type' => 'keyword',
                        ],
                        'courseTitle' => $txtTypeFieldWithCompletion,
                        'courseTerms' => $txtTypeFieldWithCompletion,
                        'courseObjectives'  => $txtTypeField,
                        'courseLearningMaterialTitles'  => $txtTypeFieldWithCompletion,
                        'courseLearningMaterialDescriptions'  => $txtTypeField,
                        'courseLearningMaterialCitation'  => $txtTypeField,
                        'courseLearningMaterialAttachments'  => $txtTypeField,
                        'courseMeshDescriptorIds' => [
                            'type' => 'keyword',
                            'fields' => [
                                'cmp' => [
                                    'type' => 'completion',
                                    // we have to override the analyzer here because the default strips
                                    // out numbers and mesh ids are mostly numbers
                                    'analyzer' => 'standard',
                                ]
                            ],
                        ],
                        'courseMeshDescriptorNames' => $txtTypeFieldWithCompletion,
                        'courseMeshDescriptorAnnotations' => $txtTypeField,
                        'sessionId' => [
                            'type' => 'keyword',
                        ],
                        'sessionTitle' => $txtTypeFieldWithCompletion,
                        'sessionDescription' => $txtTypeField,
                        'sessionType' => [
                            'type' => 'keyword',
                            'fields' => [
                                'cmp' => [
                                    'type' => 'completion'
                                ]
                            ],
                        ],
                        'sessionTerms' => $txtTypeFieldWithCompletion,
                        'sessionObjectives'  => $txtTypeField,
                        'sessionLearningMaterialTitles'  => $txtTypeFieldWithCompletion,
                        'sessionLearningMaterialDescriptions'  => $txtTypeField,
                        'sessionLearningMaterialCitation'  => $txtTypeField,
                        'sessionLearningMaterialAttachments'  => $txtTypeField,
                        'sessionMeshDescriptorIds' => [
                            'type' => 'keyword',
                            'fields' => [
                                'cmp' => [
                                    'type' => 'completion',
                                    // we have to override the analyzer here because the default strips
                                    // out numbers and mesh ids are mostly numbers
                                    'analyzer' => 'standard',
                                ]
                            ],
                        ],
                        'sessionMeshDescriptorNames' => $txtTypeFieldWithCompletion,
                        'sessionMeshDescriptorAnnotations' => $txtTypeField,
                    ]
                ]
            ]
        ];
    }

    protected static function getAnalyzers(): array
    {
        return [
            'analyzer' => [
                'ngram_analyzer' => [
                    'tokenizer' => 'ngram_tokenizer',
                    'filter' => ['lowercase'],
                ],
                'string_search_analyzer' => [
                    'type' => 'custom',
                    'tokenizer' => 'keyword',
                    'filter' => ['lowercase', 'word_delimiter'],
                ],
            ],
            'tokenizer' => [
                'ngram_tokenizer' => [
                    'type' => 'ngram',
                    'min_gram' => 3,
                    'max_gram' => 15,
                    'token_chars' => [
                        'letter',
                        'digit'
                    ],
                ],
            ],
        ];
    }
}
