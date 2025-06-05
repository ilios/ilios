<?php

declare(strict_types=1);

namespace App\Tests\Classes;

use App\Classes\MysqlMigration;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function array_filter;
use function array_map;
use function array_values;
use function implode;
use function iterator_to_array;
use function str_starts_with;
use function var_export;

final class MysqlMigrationTest extends WebTestCase
{
    public function testConstructor(): void
    {
        $kernel = self::bootKernel();
        $path = $kernel->getProjectDir() . '/migrations';
        $finder = new Finder();
        $files = $finder->in($path)->files()->depth("== 0")->sortByName();
        $classNames = array_map(
            fn(SplFileInfo $file) => 'Ilios\Migrations' . '\\' . $file->getBasename('.php'),
            array_values(iterator_to_array($files))
        );
        spl_autoload_register(function ($class) use ($path): void {
            if (str_starts_with($class, 'Ilios\\Migrations\\')) {
                $parts = explode('\\', $class);
                $baseName = end($parts);
                include "{$path}/{$baseName}.php";
            }
        });
        $classesThatDontExtendCorrectly = array_filter($classNames, function (string $name) {
            $reflection = new ReflectionClass($name);
            return !$reflection->isSubclassOf(MysqlMigration::class);
        });
        $this->assertCount(
            0,
            $classesThatDontExtendCorrectly,
            count($classesThatDontExtendCorrectly) . " migrations don't extend MysqlMigration class: " .
            var_export(array_values($classesThatDontExtendCorrectly), true)
        );
    }
}
