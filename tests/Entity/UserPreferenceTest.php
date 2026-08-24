<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Entity\UserInterface;
use App\Entity\UserPreference;
use Mockery as m;

#[Group('model')]
#[CoversClass(UserPreference::class)]
final class UserPreferenceTest extends EntityBase
{
    protected UserPreference $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new UserPreference();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotNullValidation(): void
    {
        $notNull = [
            'user',
            'json',
        ];
        $this->validateNotNulls($notNull);

        $this->object->setUser(m::mock(UserInterface::class));
        $this->object->setJson('{"key":"value"}');

        $this->validate(0);
    }

    #[DataProvider('invalidJsonProvider')]
    public function testInvalidJsonFailsValidation(string $json): void
    {
        $this->object->setUser(m::mock(UserInterface::class));
        $this->object->setJson($json);

        $errors = $this->validate(1);
        $this->assertArrayHasKey('json', $errors);
        $this->assertSame('This value should be valid JSON.', $errors['json']);
    }

    public static function invalidJsonProvider(): array
    {
        return [
            'plain text' => ['not json'],
            'unquoted key' => ['{key: "value"}'],
            'missing closing brace' => ['{"key": "value"'],
            'lone open brace' => ['{'],
            'trailing comma' => ['[1, 2,]'],
        ];
    }

    #[DataProvider('validJsonProvider')]
    public function testValidJsonPassesValidation(string $json): void
    {
        $this->object->setUser(m::mock(UserInterface::class));
        $this->object->setJson($json);

        $this->validate(0);
    }

    public static function validJsonProvider(): array
    {
        return [
            'object' => ['{"key":"value"}'],
            'empty object' => ['{}'],
            'array' => ['["a","b"]'],
            'empty array' => ['[]'],
            'number' => ['123'],
            'null literal' => ['null'],
        ];
    }

    public function testSetJson(): void
    {
        $this->basicSetTest('json', 'string');
    }

    public function testSetUser(): void
    {
        $this->entitySetTest('user', 'User');
    }

    public function getObject(): UserPreference
    {
        return $this->object;
    }
}
