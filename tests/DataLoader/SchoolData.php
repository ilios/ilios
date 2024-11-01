<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\SchoolDTO;

class SchoolData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'title' => '1foo',
            'templatePrefix' => 'foo',
            'iliosAdministratorEmail' => 'someone@example.org',
            'changeAlertRecipients' => 'sendto@example.org',
            'competencies' => ['1', '2', '3'],
            'courses' => ["1", "2"],
            'programs' => ['1', '2'],
            'instructorGroups' => ['1', '2', '3'],
            'curriculumInventoryInstitution' => "1",
            'sessionTypes' => ['1', '2'],
            'vocabularies' => ['1'],
            'directors' => ['1'],
            'administrators' => ['1'],
            'configurations' => ['1', '2'],
        ];

        $arr[] = [
            'id' => 2,
            'title' => '2bar',
            'templatePrefix' => 'ilios',
            'iliosAdministratorEmail' => 'info@example.com',
            'changeAlertRecipients' => 'example@info.org',
            'competencies' => [],
            'courses' => ["3", "4", "5"],
            'programs' => ["3"],
            'instructorGroups' => ['4'],
            'curriculumInventoryInstitution' => "2",
            'sessionTypes' => [],
            'vocabularies' => ['2'],
            'directors' => [],
            'administrators' => [],
            'configurations' => ['3'],
        ];

        $arr[] = [
            'id' => 3,
            'title' => 'third school',
            'iliosAdministratorEmail' => 'example@example.org',
            'changeAlertRecipients' => 'info@example.com',
            'competencies' => [],
            'courses' => [],
            'programs' => [],
            'instructorGroups' => [],
            'curriculumInventoryInstitution' => null,
            'sessionTypes' => [],
            'vocabularies' => [],
            'directors' => [],
            'administrators' => [],
            'configurations' => [],
        ];


        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 4,
            'title' => '4baz',
            'templatePrefix' => 'zap',
            'iliosAdministratorEmail' => 'user@info.org',
            'changeAlertRecipients' => 'example@info.org',
            'competencies' => [],
            'courses' => [],
            'programs' => [],
            'instructorGroups' => [],
            'curriculumInventoryInstitution' => null,
            'sessionTypes' => [],
            'vocabularies' => [],
            'directors' => [],
            'administrators' => [],
            'configurations' => [],
        ];
    }

    public function createInvalid(): array
    {
        return [
            'id' => 'lkjdsf',
        ];
    }

    public function createMany(int $count): array
    {
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $arr = $this->create();
            $arr['id'] = $arr['id'] + $i;
            $arr['title'] = $arr['id'] . 'title';
            unset($arr['templatePrefix']);
            $data[] = $arr;
        }

        return $data;
    }

    public function getDtoClass(): string
    {
        return SchoolDTO::class;
    }
}
