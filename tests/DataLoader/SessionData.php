<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\SessionDTO;

class SessionData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'title' => 'session1Title',
            'attireRequired' => false,
            'supplemental' => false,
            'publishedAsTbd' => false,
            'published' => true,
            'instructionalNotes' => $this->faker->text(100),
            'sessionType' => 1,
            'course' => 1,
            'description' => $this->faker->text(),
            'terms' => ['2', '5'],
            'sessionObjectives' => ['1'],
            'meshDescriptors' => ['abc1'],
            'learningMaterials' => ['1'],
            'offerings' => ['1', '2'],
            'administrators' => ['1'],
            'studentAdvisors' => [],
            'prerequisites' => [],
        ];

        $arr[] = [
            'id' => 2,
            'title' => $this->faker->text(10),
            'attireRequired' => true,
            'equipmentRequired' => true,
            'supplemental' => true,
            'attendanceRequired' => true,
            'publishedAsTbd' => true,
            'published' => true,
            'instructionalNotes' => $this->faker->text(100),
            'sessionType' => 2,
            'course' => 1,
            'description' => 'second description',
            'terms' => ['1', '4'],
            'sessionObjectives' => [],
            'meshDescriptors' => [],
            'learningMaterials' => [],
            'offerings' => ['3', '4', '5'],
            'administrators' => [],
            'studentAdvisors' => [],
            'prerequisites' => [],
            'postrequisite' => 3,

        ];

        $arr[] = [
            'id' => 3,
            'title' => $this->faker->text(10),
            'attireRequired' => false,
            'equipmentRequired' => false,
            'supplemental' => true,
            'publishedAsTbd' => false,
            'published' => false,
            'instructionalNotes' => $this->faker->text(100),
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
        ];

        $arr[] = [
            'id' => 4,
            'title' => $this->faker->text(10),
            'equipmentRequired' => false,
            'supplemental' => false,
            'attendanceRequired' => false,
            'publishedAsTbd' => false,
            'published' => false,
            'instructionalNotes' => $this->faker->text(20000),
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
        ];

        for ($i = 5; $i <= 8; $i++) {
            $ilmSession = $i - 4;
            $arr[] = [
                'id' => $i,
                'title' => $this->faker->text(10),
                'attireRequired' => false,
                'equipmentRequired' => false,
                'supplemental' => false,
                'publishedAsTbd' => false,
                'published' => false,
                'instructionalNotes' => $this->faker->text(100),
                'sessionType' => 1,
                'course' => 2,
                'ilmSession' => "${ilmSession}",
                'terms' => [],
                'sessionObjectives' => [],
                'meshDescriptors' => [],
                'learningMaterials' => [],
                'offerings' => [],
                'administrators' => [],
                'studentAdvisors' => [],
                'prerequisites' => [],
            ];
        }

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 9,
            'title' => $this->faker->text(10),
            'attireRequired' => true,
            'equipmentRequired' => true,
            'supplemental' => false,
            'attendanceRequired' => true,
            'publishedAsTbd' => true,
            'published' => true,
            'instructionalNotes' => $this->faker->text(100),
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
        ];
    }

    public function createInvalid()
    {
        return [
            'course' => 11
        ];
    }

    public function getDtoClass(): string
    {
        return SessionDTO::class;
    }
}
