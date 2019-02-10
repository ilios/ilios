<?php

namespace App\Service;

use App\Classes\ElasticSearchBase;
use App\Entity\DTO\CourseDTO;
use App\Entity\DTO\UserDTO;

class Index extends ElasticSearchBase
{
    /**
     * @param UserDTO[] $users
     * @return bool
     */
    public function indexUsers(array $users) : bool
    {
        foreach ($users as $user) {
            if (!$user instanceof UserDTO) {
                throw new \InvalidArgumentException(
                    '$users must be an array of ' . UserDTO::class . ' ' . get_class($user) . ' found'
                );
            }
        }
        $input = array_map(function (UserDTO $user) {
            return [
                'id' => $user->id,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'middleName' => $user->middleName,
                'email' => $user->email,
                'campusId' => $user->campusId,
                'username' => $user->username,
            ];
        }, $users);

        $result = $this->bulkIndex(Search::PRIVATE_INDEX, UserDTO::class, $input);

        return !$result['errors'];
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id) : bool
    {
        $result = $this->delete([
            'index' => Search::PRIVATE_INDEX,
            'type' => UserDTO::class,
            'id' => $id,
        ]);

        return !$result['errors'];
    }

    /**
     * @param CourseDTO[] $courses
     * @return bool
     */
    public function indexCourses(array $courses) : bool
    {
        foreach ($courses as $course) {
            if (!$course instanceof CourseDTO) {
                throw new \InvalidArgumentException(
                    '$courses must be an array of ' . CourseDTO::class . ' ' . get_class($course) . ' found'
                );
            }
        }
        $input = array_map(function (CourseDTO $user) {
            return [
                'id' => $user->id,
                'title' => $user->title,
            ];
        }, $courses);

        $result = $this->bulkIndex(Search::PUBLIC_INDEX, CourseDTO::class, $input);

        return !$result['errors'];
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteCourse(int $id) : bool
    {
        $result = $this->delete([
            'index' => Search::PUBLIC_INDEX,
            'type' => CourseDTO::class,
            'id' => $id,
        ]);

        return !$result['errors'];
    }

    protected function index(array $params) : array
    {
        if (!$this->enabled) {
            return ['errors' => false];
        }
        return $this->client->index($params);
    }

    protected function delete(array $params) : array
    {
        if (!$this->enabled) {
            return ['errors' => false];
        }
        return $this->client->delete($params);
    }

    protected function bulk(array $params) : array
    {
        if (!$this->enabled) {
            return ['errors' => false];
        }
        return $this->client->bulk($params);
    }

    /**
     * The API for bulk indexing is a little bit weird and front data has to be inserted in
     * front of every item. This allows bulk indexing on many types at the same time, and
     * this convenience method takes care of that for us.
     * @param $index
     * @param $type
     * @param array $items
     * @return array
     */
    protected function bulkIndex(string $index, string $type, array $items) : array
    {
        if (!$this->enabled) {
            return ['errors' => false];
        }
        $body = [];
        foreach ($items as $item) {
            $body[] = ['index' => [
                '_index' => $index,
                '_type' => $type,
                '_id' => $item['id']
            ]];
            $body[] = $item;
        }
        return $this->bulk(['body' => $body]);
    }

    public function clear()
    {
        if (!$this->enabled) {
            return;
        }
        if ($this->client->indices()->exists(['index' => self::PUBLIC_INDEX])) {
            $this->client->indices()->delete(['index' => self::PUBLIC_INDEX]);
        }
        if ($this->client->indices()->exists(['index' => self::PRIVATE_INDEX])) {
            $this->client->indices()->delete(['index' => self::PRIVATE_INDEX]);
        }
        $this->client->indices()->create(['index' => self::PUBLIC_INDEX]);
        $this->client->indices()->create(['index' => self::PRIVATE_INDEX]);
    }
}
