<?php

declare(strict_types=1);

namespace App\Service\Index;

class UserMapping
{
    public static function getBody(): array
    {
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
                        'id' => [
                            'type' => 'keyword',
                        ],
                        'firstName' => [
                            'type' => 'text',
                            'analyzer' => 'edge_ngram_analyzer',
                            'search_analyzer' => 'string_search_analyzer',
                            'fields' => [
                                'raw' => [
                                    'type' => 'keyword',
                                ]
                            ],
                        ],
                        'middleName' => [
                            'type' => 'text',
                            'analyzer' => 'edge_ngram_analyzer',
                            'search_analyzer' => 'string_search_analyzer',
                            'fields' => [
                                'raw' => [
                                    'type' => 'keyword',
                                ]
                            ],
                        ],
                        'lastName' => [
                            'type' => 'text',
                            'analyzer' => 'edge_ngram_analyzer',
                            'search_analyzer' => 'string_search_analyzer',
                            'fields' => [
                                'raw' => [
                                    'type' => 'keyword',
                                ]
                            ],
                        ],
                        'displayName' => [
                            'type' => 'text',
                            'analyzer' => 'edge_ngram_analyzer',
                            'search_analyzer' => 'string_search_analyzer',
                            'fields' => [
                                'raw' => [
                                    'type' => 'keyword',
                                ],
                                'cmp' => [
                                    'type' => 'completion'
                                ],
                            ],
                        ],
                        'fullName' => [
                            'type' => 'completion'
                        ],
                        'fullNameLastFirst' => [
                            'type' => 'completion'
                        ],
                        'username' => [
                            'type' => 'text',
                            'analyzer' => 'edge_ngram_analyzer',
                            'search_analyzer' => 'string_search_analyzer',
                            'fields' => [
                                'raw' => [
                                    'type' => 'keyword',
                                ],
                                'cmp' => [
                                    'type' => 'completion'
                                ],
                            ],
                        ],
                        'campusId' => [
                            'type' => 'keyword',
                            'fields' => [
                                'cmp' => [
                                    'type' => 'completion'
                                ]
                            ],
                        ],
                        'email' => [
                            'type' => 'text',
                            'analyzer' => 'standard',
                            'search_analyzer' => 'email_address',
                            'fields' => [
                                'cmp' => [
                                    'type' => 'completion',
                                ],
                                'email' => [
                                    'type' => 'text',
                                    'analyzer' => 'email_address',
                                ]
                            ],
                        ],
                        'enabled' => [
                            'type' => 'boolean',
                        ],
                    ]
                ]
            ]
        ];
    }

    protected static function getAnalyzers(): array
    {
        return [
            'analyzer' => [
                'edge_ngram_analyzer' => [
                    'tokenizer' => 'edge_ngram_tokenizer',
                    'filter' => ['lowercase'],
                ],
                'string_search_analyzer' => [
                    'type' => 'custom',
                    'tokenizer' => 'keyword',
                    'filter' => ['lowercase', 'word_delimiter'],
                ],
                'email_address' => [
                    'type' => 'custom',
                    'tokenizer' => 'uax_url_email',
                    'filter' => ['lowercase', 'stop'],
                ],
            ],
            'tokenizer' => [
                'edge_ngram_tokenizer' => [
                    'type' => 'edge_ngram',
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
