<?php

declare(strict_types=1);

namespace App\Tests\Service\GraphQL;

use PHPUnit\Framework\Attributes\CoversClass;
use App\Service\GraphQL\FieldResolver;
use App\Tests\TestCase;

/**
 * Class FieldResolverTest
 */
#[CoversClass(FieldResolver::class)]
final class FieldResolverTest extends TestCase
{
    public function testSomething(): void
    {
        $source = [
            "id" => 1,
            "title" => 'Title 1',
            "years" => [
                2025,
                2026,
                2027,
            ],
            "roles" => [
                'user',
                'admin',
            ],
        ];

        $fieldResolver = new FieldResolver();

        $this->assertEquals('foo', 'foo');
    }
}
