<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\SessionLearningMaterialDTO;
use DateTime;

final class SessionLearningMaterialData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'required' => true,
            'publicNotes' => false,
            'notes' => 'lorem ipsum',
            'session' => 1,
            'learningMaterial' => 1,
            'meshDescriptors' => ['abc1'],
            'position' => 1,
            'startDate' => null,
            'endDate' => null,
        ];

        $arr[] = [
            'id' => 2,
            'required' => false,
            'publicNotes' => true,
            'notes' => 'second slm',
            'session' => 3,
            'learningMaterial' => 3,
            'meshDescriptors' => ['abc2'],
            'position' => 0,
            'startDate' => null,
            'endDate' => null,
        ];

        $arr[] = [
            'id' => 3,
            'required' => false,
            'publicNotes' => true,
            'notes' => 'third slm',
            'session' => 3,
            'learningMaterial' => 5,
            'meshDescriptors' => ['abc2'],
            'position' => 0,
            'startDate' => date_format(new DateTime('2 days ago'), 'c'),
            'endDate' => null,
        ];

        $arr[] = [
            'id' => 4,
            'required' => false,
            'publicNotes' => true,
            'notes' => 'fourth slm',
            'session' => 3,
            'learningMaterial' => 6,
            'meshDescriptors' => ['abc2'],
            'position' => 0,
            'startDate' => date_format(new DateTime('+2 days'), 'c'),
            'endDate' => null,
        ];

        $arr[] = [
            'id' => 5,
            'required' => false,
            'publicNotes' => true,
            'notes' => 'fifth slm',
            'session' => 3,
            'learningMaterial' => 7,
            'meshDescriptors' => ['abc2'],
            'position' => 0,
            'startDate' => null,
            'endDate' => date_format(new DateTime('+ 2 days'), 'c'),
        ];

        $arr[] = [
            'id' => 6,
            'required' => false,
            'publicNotes' => true,
            'notes' => 'sixth slm',
            'session' => 3,
            'learningMaterial' => 8,
            'meshDescriptors' => ['abc2'],
            'position' => 0,
            'startDate' => null,
            'endDate' => date_format(new DateTime('2 days ago'), 'c'),
        ];

        $arr[] = [
            'id' => 7,
            'required' => false,
            'publicNotes' => true,
            'notes' => 'seventh slm',
            'session' => 3,
            'learningMaterial' => 9,
            'meshDescriptors' => ['abc2'],
            'position' => 0,
            'startDate' => date_format(new DateTime('2 days ago'), 'c'),
            'endDate' => date_format(new DateTime('+2 days'), 'c'),
        ];

        $arr[] = [
            'id' => 8,
            'required' => false,
            'publicNotes' => true,
            'notes' => 'eighth slm',
            'session' => 3,
            'learningMaterial' => 10,
            'meshDescriptors' => ['abc2'],
            'position' => 0,
            'startDate' => date_format(new DateTime('4 days ago'), 'c'),
            'endDate' => date_format(new DateTime('2 days ago'), 'c'),
        ];

        $arr[] = [
            'id' => 9,
            'required' => true,
            'publicNotes' => true,
            'notes' => '',
            'session' => 2,
            'learningMaterial' => 10,
            'meshDescriptors' => [],
            'position' => 1,
            'startDate' => null,
            'endDate' => null,
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 10,
            'required' => false,
            'notes' => 'foo bar baz',
            'publicNotes' => false,
            'session' => 1,
            'learningMaterial' => 2,
            'meshDescriptors' => [],
            'position' => 0,
            'startDate' => null,
            'endDate' => null,
        ];
    }

    public function createInvalid(): array
    {
        return [
            'session' => 11,
        ];
    }

    public function getDtoClass(): string
    {
        return SessionLearningMaterialDTO::class;
    }
}
