<?php

namespace App\Service;

use App\Classes\ElasticSearchBase;
use App\Entity\DTO\CourseDTO;
use App\Entity\DTO\UserDTO;
use Ilios\MeSH\Model\Concept;
use Ilios\MeSH\Model\Descriptor;

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

        $result = $this->bulkIndex(Search::USER_INDEX, UserDTO::class, $input);

        return !$result['errors'];
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id) : bool
    {
        $result = $this->delete([
            'index' => Search::USER_INDEX,
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
        $input = array_map(function (CourseDTO $course) {
            return [
                'id' => $course->id,
                'title' => $course->title,
            ];
        }, $courses);

        $result = $this->bulkIndex(Search::COURSE_INDEX, CourseDTO::class, $input);

        return !$result['errors'];
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteCourse(int $id) : bool
    {
        $result = $this->delete([
            'index' => Search::COURSE_INDEX,
            'type' => CourseDTO::class,
            'id' => $id,
        ]);

        return !$result['errors'];
    }

    /**
     * @param Descriptor[] $descriptors
     * @return bool
     */
    public function indexMeshDescriptors(array $descriptors) : bool
    {
        foreach ($descriptors as $descriptor) {
            if (!$descriptor instanceof Descriptor) {
                throw new \InvalidArgumentException(
                    '$descriptors must be an array of ' . Descriptor::class . ' ' . get_class($descriptor) . ' found'
                );
            }
        }

        $input = array_map(function (Descriptor $descriptor) {
            $conceptMap = array_reduce($descriptor->getConcepts(), function (array $carry, Concept $concept) {
                $carry['conceptNames'][] = $concept->getName();
                $carry['scopeNotes'][] = $concept->getScopeNote();
                $carry['casn1Names'][] = $concept->getCasn1Name();
                foreach ($concept->getTerms() as $term) {
                    $carry['termNames'][] = $term->getName();
                }

                return $carry;
            }, [
                'conceptNames' => [],
                'termNames' => [],
                'scopeNotes' => [],
                'casn1Names' => [],
            ]);

            return [
                'id' => $descriptor->getUi(),
                'name' => $descriptor->getName(),
                'annotation' => $descriptor->getAnnotation(),
                'previousIndexing' => join(' ', $descriptor->getPreviousIndexing()),
                'terms' => join(' ', $conceptMap['termNames']),
                'concepts' => join(' ', $conceptMap['conceptNames']),
                'scopeNotes' => join(' ', $conceptMap['scopeNotes']),
                'casn1Names' => join(' ', $conceptMap['casn1Names']),
            ];
        }, $descriptors);

        $result = $this->bulkIndex(Search::MESH_INDEX, Descriptor::class, $input);
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
        $indexes = [
            self::COURSE_INDEX,
            self::MESH_INDEX,
            self::USER_INDEX,
        ];
        foreach ($indexes as $index) {
            if ($this->client->indices()->exists(['index' => $index])) {
                $this->client->indices()->delete(['index' => $index]);
            }
            $this->client->indices()->create(['index' => $index]);
        }
    }
}
