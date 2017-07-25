<?php

namespace Tests\CliBundle\DataLoader;

use Tests\CoreBundle\DataLoader\AbstractDataLoader;

/**
 * Class AuditLogData
 */
class AuditLogData extends AbstractDataLoader
{
    /**
     * {@inheritdoc}
     */
    protected function getData()
    {
        $arr[] = [
            'createdAt' => new \DateTime('1 day ago', new \DateTimeZone('UTC')),
            // bogus class name, we'll use this to peel entries out of the command output by this.
            'objectClass' => 'YesterdaysEvent',
            'action' => $this->faker->text(10),
            'valuesChanged' => $this->faker->text(10),
            'objectId' => $this->faker->randomDigitNotNull,
        ];

        $arr[] = [
            'createdAt' => new \DateTime('1 year ago', new \DateTimeZone('UTC')),
            'objectClass' => 'LastYearsEvent',
            'action' => $this->faker->text(10),
            'valuesChanged' => $this->faker->text(10),
            'objectId' => $this->faker->randomDigitNotNull,
        ];

        $arr[] = [
            'createdAt' => new \DateTime('midnight today', new \DateTimeZone('UTC')),
            'objectClass' => 'TodaysEvent',
            'action' => $this->faker->text(10),
            'valuesChanged' => $this->faker->text(10),
            'objectId' => $this->faker->randomDigitNotNull,
        ];

        return $arr;
    }

    /**
     * Not implemented.
     *
     * @throws \Exception
     */
    public function create()
    {
        throw new \Exception('Not implemented');
    }

    /**
     * Not implemented.
     *
     * @throws \Exception
     */
    public function createInvalid()
    {
        throw new \Exception('Not implemented');
    }
}
