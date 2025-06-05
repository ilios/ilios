<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\CurriculumInventorySequenceBlockInterface;
use App\Entity\DTO\CurriculumInventorySequenceBlockDTO;
use DateTime;

final class CurriculumInventorySequenceBlockData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];
        $dt = new DateTime();
        $dt->setTime(0, 0, 0);
        $arr[] = [
            'id' => 1,
            'title' => 'Top Level Sequence Block 1',
            'description' => 'first description',
            'report' => 1,
            'childSequenceOrder' => CurriculumInventorySequenceBlockInterface::ORDERED,
            'orderInSequence' => 0,
            'startingAcademicLevel' => 1,
            'endingAcademicLevel' => 2,
            'minimum' => 1,
            'maximum' => 1,
            'duration' => 2,
            'required' => CurriculumInventorySequenceBlockInterface::REQUIRED,
            'startDate' => $dt->format('c'),
            'endDate' => $dt->format('c'),
            'children' => ['2', '3', '4', '5'],
            'sessions' => ['1'],
            'excludedSessions' => ['1'],
            'parent' => null,
            'track' => true,
            'course' => null,
        ];
        for ($i = 1; $i < 5; $i++) {
            $arr[] = [
                'id' => $i + 1,
                'title' => 'Nested Sequence Block ' . $i,
                'report' => 1,
                'childSequenceOrder' => CurriculumInventorySequenceBlockInterface::OPTIONAL,
                'orderInSequence' => $i,
                'startingAcademicLevel' => 2,
                'endingAcademicLevel' => 3,
                'minimum' => 1,
                'maximum' => 1,
                'duration' => 1,
                'required' => CurriculumInventorySequenceBlockInterface::OPTIONAL,
                'startDate' => $dt->format('c'),
                'endDate' => $dt->format('c'),
                'children' => [],
                'sessions' => [],
                'excludedSessions' => [],
                'parent' => 1,
                'track' => false,
                'course' => null,
            ];
        }

        return $arr;
    }

    public function create(): array
    {
        $dt = new DateTime();
        $dt->setTime(0, 0, 0);
        return [
            'id' => 6,
            'title' => 'sixth sequence block',
            'report' => 1,
            'childSequenceOrder' => CurriculumInventorySequenceBlockInterface::ORDERED,
            'orderInSequence' => 1,
            'startingAcademicLevel' => 2,
            'endingAcademicLevel' => 3,
            'minimum' => 1,
            'maximum' => 1,
            'duration' => 1,
            'required' => CurriculumInventorySequenceBlockInterface::REQUIRED_IN_TRACK,
            'startDate' => $dt->format('c'),
            'endDate' => $dt->format('c'),
            'children' => [],
            'sessions' => [],
            'excludedSessions' => [],
            'parent' => 1,
            'track' => true,
            'course' => null,
        ];
    }

    public function createInvalid(): array
    {
        return [
            'id' => 7,
        ];
    }

    public function getDtoClass(): string
    {
        return CurriculumInventorySequenceBlockDTO::class;
    }
}
