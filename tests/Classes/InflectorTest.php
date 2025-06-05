<?php

declare(strict_types=1);

namespace App\Tests\Classes;

use PHPUnit\Framework\Attributes\Group;
use App\Service\InflectorFactory;
use PHPUnit\Framework\TestCase;

final class InflectorTest extends TestCase
{
    #[Group('twice')]
    public function testInstanceInflection(): void
    {
        $singular = 'aamcPcrs';
        $plural = 'aamcPcrses';
        $inflector = InflectorFactory::create();
        $inflectedPlural = $inflector->pluralize($singular);
        $inflectedSingular = $inflector->singularize($plural);

        $this->assertEquals($singular, $inflectedSingular, 'correctly singularized');
        $this->assertEquals($plural, $inflectedPlural, 'correctly pluralized');

        $unInflectedPlural = $inflector->pluralize($plural);
        $unInflectedSingular = $inflector->singularize($singular);

        $this->assertEquals($singular, $unInflectedSingular, 'correctly singularized');
        $this->assertEquals($plural, $unInflectedPlural, 'correctly pluralized');

        $camelPlural = $inflector->camelize('AamcPcrses');
        $this->assertSame($camelPlural, 'aamcPcrses');

        $camelSingular = $inflector->camelize('AamcPcrs');
        $this->assertSame($camelSingular, 'aamcPcrs');
    }

    #[Group('twice')]
    public function testLowerCaseInstanceInflection(): void
    {
        $singular = 'aamcpcrs';
        $plural = 'aamcpcrses';
        $inflector = InflectorFactory::create();
        $inflectedPlural = $inflector->pluralize($singular);
        $inflectedSingular = $inflector->singularize($plural);

        $this->assertEquals($singular, $inflectedSingular, 'correctly singularized');
        $this->assertEquals($plural, $inflectedPlural, 'correctly pluralized');

        $unInflectedPlural = $inflector->pluralize($plural);
        $unInflectedSingular = $inflector->singularize($singular);

        $this->assertEquals($singular, $unInflectedSingular, 'correctly singularized');
        $this->assertEquals($plural, $unInflectedPlural, 'correctly pluralized');

        $camelPlural = $inflector->camelize('aamcpcrses');
        $this->assertSame($camelPlural, 'aamcpcrses');

        $camelSingular = $inflector->camelize('aamcpcrs');
        $this->assertSame($camelSingular, 'aamcpcrs');
    }
}
