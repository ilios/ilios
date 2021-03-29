<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\SchoolDTO;

class SchoolData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'title' => '1' . $this->faker->text(59),
            'templatePrefix' => $this->faker->text(8),
            'iliosAdministratorEmail' => $this->faker->email(),
            'changeAlertRecipients' => $this->faker->email(),
            'competencies' => ['1', '2', '3'],
            'courses' => ["1", "2"],
            'programs' => ['1', '2'],
            'instructorGroups' => ['1', '2', '3'],
            'curriculumInventoryInstitution' => "1",
            'sessionTypes' => ['1', '2'],
            'vocabularies' => ['1'],
            'directors' => ['1'],
            'administrators' => ['1'],
            'configurations' => ['1', '2']
        ];

        $arr[] = [
            'id' => 2,
            'title' => '2' . $this->faker->word(),
            'templatePrefix' => $this->faker->text(8),
            'iliosAdministratorEmail' => 'info@example.com',
            'changeAlertRecipients' => $this->faker->email(),
            'competencies' => [],
            'courses' => ["3", "4", "5"],
            'programs' => ["3"],
            'instructorGroups' => ['4'],
            'curriculumInventoryInstitution' => "2",
            'sessionTypes' => [],
            'vocabularies' => ['2'],
            'directors' => [],
            'administrators' => [],
            'configurations' => ['3']
        ];

        $arr[] = [
            'id' => 3,
            'title' => 'third school',
            'iliosAdministratorEmail' => $this->faker->email(),
            'changeAlertRecipients' => 'info@example.com',
            'competencies' => [],
            'courses' => [],
            'programs' => [],
            'instructorGroups' => [],
            'sessionTypes' => [],
            'vocabularies' => [],
            'directors' => [],
            'administrators' => [],
            'configurations' => []
        ];


        return $arr;
    }

    public function create()
    {
        return [
            'id' => 4,
            'title' => '4' . $this->faker->word(),
            'templatePrefix' => $this->faker->text(8),
            'iliosAdministratorEmail' => $this->faker->email(),
            'changeAlertRecipients' => $this->faker->email(),
            'competencies' => [],
            'courses' => [],
            'programs' => [],
            'instructorGroups' => [],
            'sessionTypes' => [],
            'vocabularies' => [],
            'directors' => [],
            'administrators' => [],
            'configurations' => [],
        ];
    }

    public function createInvalid()
    {
        return [
            'id' => 'lkjdsf'
        ];
    }

    /**
     * @inheritdoc
     */
    public function createMany($count)
    {
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $arr = $this->create();
            $arr['id'] = $arr['id'] + $i;
            $arr['title'] = $arr['id'] . $this->faker->word();
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
