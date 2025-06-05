<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\SessionDTO;

final class SessionData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'title' => 'session1Title',
            'attireRequired' => false,
            'supplemental' => false,
            'publishedAsTbd' => false,
            'published' => true,
            'instructionalNotes' => 'lorem ipsum',
            'sessionType' => 1,
            'course' => 1,
            'description' => 'foo bar',
            'terms' => ['2', '5'],
            'sessionObjectives' => ['1'],
            'meshDescriptors' => ['abc1'],
            'learningMaterials' => ['1'],
            'offerings' => ['1', '2'],
            'administrators' => ['1'],
            'studentAdvisors' => [],
            'prerequisites' => [],
            'ilmSession' => null,
            'postrequisite' => null,
        ];

        $arr[] = [
            'id' => 2,
            'title' => 'foo bar',
            'attireRequired' => true,
            'equipmentRequired' => true,
            'supplemental' => true,
            'attendanceRequired' => true,
            'publishedAsTbd' => true,
            'published' => true,
            'instructionalNotes' => 'sample text',
            'sessionType' => 2,
            'course' => 1,
            'description' => 'second description',
            'terms' => ['1', '4'],
            'sessionObjectives' => [],
            'meshDescriptors' => [],
            'learningMaterials' => ['9'],
            'offerings' => ['3', '4', '5'],
            'administrators' => [],
            'studentAdvisors' => [],
            'prerequisites' => [],
            'postrequisite' => 3,
            'ilmSession' => null,
        ];

        $arr[] = [
            'id' => 3,
            'title' => 'third session',
            'attireRequired' => false,
            'equipmentRequired' => false,
            'supplemental' => true,
            'publishedAsTbd' => false,
            'published' => false,
            'instructionalNotes' => 'foo bar baz',
            'sessionType' => 2,
            'course' => 2,
            'terms' => ['3', '6'],
            'sessionObjectives' => [],
            'meshDescriptors' => ["abc2"],
            'learningMaterials' => ['2', '3', '4', '5', '6', '7', '8'],
            'offerings' => ['6', '7', '8'],
            'administrators' => [],
            'studentAdvisors' => [],
            'prerequisites' => ['2', '4'],
            'postrequisite' => null,
            'ilmSession' => null,
        ];

        $arr[] = [
            'id' => 4,
            'title' => 'fourth session',
            'equipmentRequired' => false,
            'supplemental' => false,
            'attendanceRequired' => false,
            'publishedAsTbd' => false,
            'published' => false,
            'instructionalNotes' => 'some text',
            'sessionType' => 2,
            'course' => 4,
            'terms' => [],
            'sessionObjectives' => ['2', '3'],
            'meshDescriptors' => [],
            'learningMaterials' => [],
            'offerings' => [],
            'administrators' => [],
            'studentAdvisors' => ['3'],
            'prerequisites' => [],
            'postrequisite' => 3,
            'ilmSession' => null,
        ];

        for ($i = 5; $i <= 8; $i++) {
            $ilmSession = $i - 4;
            $arr[] = [
                'id' => $i,
                'title' => 'session title',
                'attireRequired' => false,
                'equipmentRequired' => false,
                'supplemental' => false,
                'publishedAsTbd' => false,
                'published' => false,
                'instructionalNotes' => 'foo bar',
                'sessionType' => 1,
                'course' => 2,
                'ilmSession' => "{$ilmSession}",
                'terms' => [],
                'sessionObjectives' => [],
                'meshDescriptors' => [],
                'learningMaterials' => [],
                'offerings' => [],
                'administrators' => [],
                'studentAdvisors' => [],
                'prerequisites' => [],
                'postrequisite' => null,
            ];
        }

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 9,
            'title' => 'ninth session',
            'attireRequired' => true,
            'equipmentRequired' => true,
            'supplemental' => false,
            'attendanceRequired' => true,
            'publishedAsTbd' => true,
            'published' => true,
            'instructionalNotes' => 'text text',
            'sessionType' => 1,
            'course' => 1,
            'terms' => ['1', '2'],
            'sessionObjectives' => [],
            'meshDescriptors' => [],
            'learningMaterials' => [],
            'offerings' => [],
            'administrators' => [],
            'studentAdvisors' => [],
            'prerequisites' => [],
            'ilmSession' => null,
            'postrequisite' => null,
        ];
    }

    public function createInvalid(): array
    {
        return [
            'course' => 11,
        ];
    }

    public function getDtoClass(): string
    {
        return SessionDTO::class;
    }
}
