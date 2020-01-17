<?php

declare(strict_types=1);

namespace App\Service\Index;

use App\Classes\ElasticSearchBase;
use App\Entity\DTO\UserDTO;
use InvalidArgumentException;
use Exception;

class Users extends ElasticSearchBase
{
    /**
     * @param UserDTO[] $users
     * @return bool
     */
    public function index(array $users): bool
    {
        foreach ($users as $user) {
            if (!$user instanceof UserDTO) {
                throw new InvalidArgumentException(
                    sprintf(
                        '$users must be an array of %s. %s found',
                        UserDTO::class,
                        get_class($user)
                    )
                );
            }
        }
        $input = array_map(function (UserDTO $user) {
            return [
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
            ];
        }, $users);

        $result = $this->doBulkIndex(self::USER_INDEX, $input);

        return !$result['errors'];
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $result = $this->doDelete([
            'index' => self::USER_INDEX,
            'id' => $id,
        ]);

        return $result['result'] === 'deleted';
    }

    public function search(string $query, int $size, bool $onlySuggest)
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
                    'field' => "${field}",
                    'skip_duplicates' => true,
                ]
            ];

            return $carry;
        }, []);


        $params = [
            'type' => '_doc',
            'index' => self::USER_INDEX,
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
                'sort' => '_score'
            ]
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
                    ]
                ]
            ];
        }

        $results = $this->doSearch($params);

        $autocompleteSuggestions = array_reduce(
            $results['suggest'],
            function (array $carry, array $item) {
                $options = array_map(function (array $arr) {
                    return $arr['text'];
                }, $item[0]['options']);

                return array_unique(array_merge($carry, $options));
            },
            []
        );

        $users = array_column($results['hits']['hits'], '_source');

        return [
            'autocomplete' => $autocompleteSuggestions,
            'users' => $users
        ];
    }
}
