<?php

declare(strict_types=1);

namespace App\Tests\Classes;

use App\Classes\Inflector;
use PHPUnit\Framework\TestCase;

class InflectorTest extends TestCase
{
    /**
     * @group twice
     */
    public function testInflection()
    {
        $singular = 'aamcPcrs';
        $plural = 'aamcPcrses';
        $inflectedPlural = Inflector::pluralize($singular);
        $inflectedSingular = Inflector::singularize($plural);

        $this->assertEquals($singular, $inflectedSingular, 'correctly singularized');
        $this->assertEquals($plural, $inflectedPlural, 'correctly pluralized');

        $unInflectedPlural = Inflector::pluralize($plural);
        $unInflectedSingular = Inflector::singularize($singular);

        $this->assertEquals($singular, $unInflectedSingular, 'correctly singularized');
        $this->assertEquals($plural, $unInflectedPlural, 'correctly pluralized');

        $camelPlural = Inflector::camelize('AamcPcrses');
        $this->assertSame($camelPlural, 'aamcPcrses');

        $camelSingular = Inflector::camelize('AamcPcrs');
        $this->assertSame($camelSingular, 'aamcPcrs');
    }

    /**
     * @group twice
     */
    public function testLowerCaseInflection()
    {
        $singular = 'aamcpcrs';
        $plural = 'aamcpcrses';
        $inflectedPlural = Inflector::pluralize($singular);
        $inflectedSingular = Inflector::singularize($plural);

        $this->assertEquals($singular, $inflectedSingular, 'correctly singularized');
        $this->assertEquals($plural, $inflectedPlural, 'correctly pluralized');

        $unInflectedPlural = Inflector::pluralize($plural);
        $unInflectedSingular = Inflector::singularize($singular);

        $this->assertEquals($singular, $unInflectedSingular, 'correctly singularized');
        $this->assertEquals($plural, $unInflectedPlural, 'correctly pluralized');

        $camelPlural = Inflector::camelize('aamcpcrses');
        $this->assertSame($camelPlural, 'aamcpcrses');

        $camelSingular = Inflector::camelize('aamcpcrs');
        $this->assertSame($camelSingular, 'aamcpcrs');
    }
}
