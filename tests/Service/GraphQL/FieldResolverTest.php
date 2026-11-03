<?php

declare(strict_types=1);

namespace App\Tests\Service\GraphQL;

use PHPUnit\Framework\Attributes\CoversClass;
use App\Service\GraphQL\FieldResolver;
use App\Tests\TestCase;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Class FieldResolverTest
 */
#[CoversClass(FieldResolver::class)]
final class FieldResolverTest extends TestCase
{
    private FieldResolver $resolver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->resolver = new FieldResolver();
    }

    private function resolveInfo(string $fieldName): ResolveInfo
    {
        /** @var ResolveInfo $info */
        $info = (new \ReflectionClass(ResolveInfo::class))
            ->newInstanceWithoutConstructor();

        $info->fieldName = $fieldName;

        return $info;
    }

    public function testArraySourceWithExistingField(): void
    {
        $source = ['name' => 'Lothar'];
        $result = ($this->resolver)(
            $source,
            [],
            null,
            $this->resolveInfo('name')
        );

        $this->assertSame('Lothar', $result);
    }

    public function testArraySourceWithoutField(): void
    {
        $source = ['other' => 'value'];
        $result = ($this->resolver)(
            $source,
            [],
            null,
            $this->resolveInfo('missing')
        );

        $this->assertNull($result);
    }

    public function testObjectSourceWithProperty(): void
    {
        $obj = (object)['age' => 152];
        $result = ($this->resolver)(
            $obj,
            [],
            null,
            $this->resolveInfo('age')
        );

        $this->assertSame(152, $result);
    }

    public function testObjectSourceWithoutProperty(): void
    {
        $obj = (object)['other' => 'x'];
        $result = ($this->resolver)(
            $obj,
            [],
            null,
            $this->resolveInfo('nonexistent')
        );

        $this->assertNull($result);
    }

    public function testClosureIsInvokedAndReturned(): void
    {
        $source = [
            'value' => static fn($src, $args, $ctx, $info) => 'computed-' . $src['id'],
            'id'    => 7,
        ];

        $result = ($this->resolver)(
            $source,
            [],
            null,
            $this->resolveInfo('value')
        );

        $this->assertSame('computed-7', $result);
    }

    public function testClosureReceivesAllParameters(): void
    {
        $source = [
            'data' => static fn($src, $a, $ctx, $info) => implode(
                '|',
                [
                    json_encode($src),
                    json_encode($a),
                    json_encode($ctx),
                    $info->fieldName,
                ]
            ),
            'x'    => 1,
        ];

        $args     = ['argA' => 'foobar'];
        $context  = ['user' => 'boofar'];

        $result = ($this->resolver)(
            $source,
            $args,
            $context,
            $this->resolveInfo('data')
        );

        $this->assertStringContainsString('"x":1', $result);
        $this->assertStringContainsString(json_encode($args), $result);
        $this->assertStringContainsString(json_encode($context), $result);
        $this->assertStringEndsWith('|data', $result);
    }
}
