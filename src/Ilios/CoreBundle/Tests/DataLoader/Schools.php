<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class Schools extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[1] = array(
          'id' => 1,
          'title' => "Medicine",
          'iliosAdministratorEmail' => "ilios_admin@example.edu",
          'deleted' => false,
          'changeAlertRecipients' => "ilios_admin@example.edu",
          'alerts' => [],
          'competencies' => [
            '1',
          ],
          'courses' => [
            '1',
          ],
          'programs' => [
            '1',
          ],
          'departments' => [
            '1',
          ],
          'disciplines' => [
            '2',
          ],
          'instructorGroups' => [
            '2',
          ],
          'curriculumInventoryInsitution' => "1",
          'sessionTypes' => [
            '19',
            '21',
          ]
        );

        return $arr;
    }

    public function create()
    {
        return [];
    }

    public function createInvalid()
    {
        return [];
    }
}
