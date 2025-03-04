<?php

declare(strict_types=1);

namespace App\Service\Index;

use App\Classes\OpenSearchBase;
use App\Entity\DTO\UserDTO;
use DateTime;
use InvalidArgumentException;
use Exception;

class Users extends OpenSearchBase
{
    public const string INDEX = 'ilios-users';

    public function index(array $users): bool
    {
        foreach ($users as $user) {
            if (!$user instanceof UserDTO) {
                throw new InvalidArgumentException(
                    sprintf(
                        '$users must be an array of %s. %s found',
                        UserDTO::class,
                        $user::class
                    )
                );
            }
        }
        $input = array_map(fn(UserDTO $user) => [
            'id' => $user->id,
            'firstName' => $user->firstName,
            'lastName' => $user->lastName,
            'middleName' => $user->middleName,
            'displayName' => $user->displayName,
            'email' => $user->email,
            'campusId' => $user->campusId,
            'username' => $user->username,
            'enabled' => $user->enabled,
            'fullName' => $user->firstName . ' ' . $user->middleName . ' ' . $user->lastName,
            'fullNameLastFirst' => $user->lastName . ', ' . $user->firstName . ' ' . $user->middleName,
        ], $users);

        return $this->doBulkIndex(self::INDEX, $input);
    }

    public function delete(int $id): bool
    {
        $result = $this->doDelete([
            'index' => self::INDEX,
            'id' => $id,
        ]);

        return $result['result'] === 'deleted';
    }

    public function search(string $query, int $size, bool $onlySuggest): array
    {
        if (!$this->enabled) {
            throw new Exception("Search is not configured, isEnabled() should be called before calling this method");
        }

        $suggestFields = [
            'fullName',
            'fullNameLastFirst',
            'email.cmp',
            'campusId.cmp',
            'username.cmp',
        ];
        $suggest = array_reduce($suggestFields, function ($carry, $field) use ($query) {
            $carry[$field] = [
                'prefix' => $query,
                'completion' => [
                    'field' => "{$field}",
                    'skip_duplicates' => true,
                ],
            ];

            return $carry;
        }, []);


        $params = [
            'index' => self::INDEX,
            'size' => $size,
            'body' => [
                'suggest' => $suggest,
                "_source" => [
                    'id',
                    'firstName',
                    'middleName',
                    'lastName',
                    'displayName',
                    'campusId',
                    'email',
                    'enabled',
                ],
                'sort' => '_score',
            ],
        ];

        if (!$onlySuggest) {
            $params['body']['query'] = [
                'multi_match' => [
                    'query' => $query,
                    'type' => 'most_fields',
                    'fields' => [
                        'firstName',
                        'firstName.raw^3',
                        'middleName',
                        'middleName.raw^3',
                        'lastName',
                        'lastName.raw^3',
                        'displayName',
                        'displayName.raw^3',
                        'username^3',
                        'username.raw^5',
                        'campusId^5',
                        'email',
                        'email.email^5',
                    ],
                ],
            ];
        }

        $results = $this->doSearch($params);

        $autocompleteSuggestions = array_reduce(
            $results['suggest'],
            function (array $carry, array $item) {
                $options = array_map(fn(array $arr) => $arr['text'], $item[0]['options']);

                return array_unique(array_merge($carry, $options));
            },
            []
        );

        $users = array_column($results['hits']['hits'], '_source');

        return [
            'autocomplete' => $autocompleteSuggestions,
            'users' => $users,
        ];
    }
    public static function getMapping(): array
    {
        return [
            'settings' => [
                'analysis' => self::getAnalyzers(),
                'default_pipeline' => 'users',
                'max_ngram_diff' =>  15,
                'number_of_shards' => 1,
                'number_of_replicas' => 1,
            ],
            'mappings' => [
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
                            ],
                        ],
                    ],
                    'middleName' => [
                        'type' => 'text',
                        'analyzer' => 'edge_ngram_analyzer',
                        'search_analyzer' => 'string_search_analyzer',
                        'fields' => [
                            'raw' => [
                                'type' => 'keyword',
                            ],
                        ],
                    ],
                    'lastName' => [
                        'type' => 'text',
                        'analyzer' => 'edge_ngram_analyzer',
                        'search_analyzer' => 'string_search_analyzer',
                        'fields' => [
                            'raw' => [
                                'type' => 'keyword',
                            ],
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
                                'type' => 'completion',
                            ],
                        ],
                    ],
                    'fullName' => [
                        'type' => 'completion',
                    ],
                    'fullNameLastFirst' => [
                        'type' => 'completion',
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
                                'type' => 'completion',
                            ],
                        ],
                    ],
                    'campusId' => [
                        'type' => 'keyword',
                        'fields' => [
                            'cmp' => [
                                'type' => 'completion',
                            ],
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
                            ],
                        ],
                    ],
                    'enabled' => [
                        'type' => 'boolean',
                    ],
                    'ingestTime' => [
                        'type' => 'date',
                        'format' => 'date_optional_time||basic_date_time_no_millis||epoch_second||epoch_millis',
                    ],
                ],
            ],
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
                        'digit',
                    ],
                ],
            ],
        ];
    }

    public static function getPipeline(): array
    {
        return [
            'id' => 'users',
            'body' => [
                'description' => 'Set Ingest Time',
                'processors' => [
                    [
                        'set' => [
                            'field' => '_source.ingestTime',
                            'value' => '{{_ingest.timestamp}}',
                        ],
                    ],
                ],
            ],
        ];
    }
}
