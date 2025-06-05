<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\CourseLearningMaterialDTO;
use DateTime;

final class CourseLearningMaterialData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'required' => true,
            'publicNotes' => true,
            'notes' => 'lorem ipsum',
            'course' => "1",
            'learningMaterial' => "1",
            'meshDescriptors' => ['abc1'],
            'position' => 0,
            'startDate' => null,
            'endDate' => null,
        ];

        $arr[] = [
            'id' => 2,
            'required' => false,
            'publicNotes' => true,
            'notes' => 'dev/null',
            'course' => "1",
            'learningMaterial' => "2",
            'meshDescriptors' => [],
            'position' => 1,
            'startDate' => null,
            'endDate' => null,
        ];

        $arr[] = [
            'id' => 3,
            'required' => true,
            'publicNotes' => false,
            'notes' => 'third note',
            'course' => "4",
            'learningMaterial' => "1",
            'meshDescriptors' => ['abc1'],
            'position' => 0,
            'startDate' => null,
            'endDate' => null,
        ];

        $arr[] = [
            'id' => 4,
            'required' => true,
            'publicNotes' => true,
            'notes' => 'fourth note',
            'course' => "1",
            'learningMaterial' => "3",
            'meshDescriptors' => [],
            'position' => 2,
            'startDate' => null,
            'endDate' => null,
        ];

        $arr[] = [
            'id' => 5,
            'required' => true,
            'publicNotes' => true,
            'notes' => 'fifth note',
            'course' => "1",
            'learningMaterial' => "5",
            'meshDescriptors' => [],
            'position' => 3,
            'startDate' => date_format(new DateTime('2 days ago'), 'c'),
            'endDate' => null,
        ];

        $arr[] = [
            'id' => 6,
            'required' => true,
            'publicNotes' => true,
            'notes' => 'sixth note',
            'course' => "1",
            'learningMaterial' => "6",
            'meshDescriptors' => [],
            'position' => 4,
            'startDate' => date_format(new DateTime('+2 days'), 'c'),
            'endDate' => null,
        ];

        $arr[] = [
            'id' => 7,
            'required' => true,
            'publicNotes' => true,
            'notes' => 'seventh note',
            'course' => "1",
            'learningMaterial' => "7",
            'meshDescriptors' => [],
            'position' => 5,
            'startDate' => null,
            'endDate' => date_format(new DateTime('+2 days'), 'c'),
        ];

        $arr[] = [
            'id' => 8,
            'required' => true,
            'publicNotes' => true,
            'notes' => 'eighth note',
            'course' => "1",
            'learningMaterial' => "8",
            'meshDescriptors' => [],
            'position' => 6,
            'startDate' => null,
            'endDate' => date_format(new DateTime('2 days ago'), 'c'),
        ];

        $arr[] = [
            'id' => 9,
            'required' => true,
            'publicNotes' => true,
            'notes' => 'ninth note',
            'course' => "1",
            'learningMaterial' => "9",
            'meshDescriptors' => [],
            'position' => 7,
            'startDate' => date_format(new DateTime('2 days ago'), 'c'),
            'endDate' => date_format(new DateTime('+2 days'), 'c'),
        ];

        $arr[] = [
            'id' => 10,
            'required' => true,
            'publicNotes' => true,
            'notes' => 'tenth note',
            'course' => "1",
            'learningMaterial' => "10",
            'meshDescriptors' => [],
            'position' => 8,
            'startDate' => date_format(new DateTime('4 days ago'), 'c'),
            'endDate' => date_format(new DateTime('2 days ago'), 'c'),
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 11,
            'required' => true,
            'publicNotes' => true,
            'notes' => 'eleventh note',
            'course' => "1",
            'learningMaterial' => "2",
            'meshDescriptors' => [],
            'position' => 0,
            'startDate' => null,
            'endDate' => null,
        ];
    }

    public function createInvalid(): array
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return CourseLearningMaterialDTO::class;
    }
}
