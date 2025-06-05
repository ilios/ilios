<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use App\Command\FindUserCommand;
use App\Service\Directory;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class FindUserCommandTest
 * @package App\Tests\Command
 */
#[Group('cli')]
final class FindUserCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected m\MockInterface $directory;

    public function setUp(): void
    {
        parent::setUp();
        $this->directory = m::mock(Directory::class);
        $command = new FindUserCommand($this->directory);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find($command->getName());
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * Remove all mock objects
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->directory);
        unset($this->commandTester);
    }

    public function testExecute(): void
    {
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'displayName' => 'first last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'preferredFirstName' => null,
            'preferredLastName' => null,
        ];
        $this->directory->shouldReceive('find')->with(['a', 'b'])->andReturn([$fakeDirectoryUser]);

        $this->commandTester->execute([
            'searchTerms' => ['a', 'b'],
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/abc\s+\| first last\s+\| first\s+\| last\s+\| email\s+\| phone/',
            $output
        );
    }

    public function testExecuteWithPreferredNames(): void
    {
        $fakeDirectoryUser = [
            'firstName' => 'first',
            'lastName' => 'last',
            'displayName' => 'first last',
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
            'preferredFirstName' => 'preferredFirst',
            'preferredLastName' => 'preferredLast',
        ];
        $this->directory->shouldReceive('find')->with(['a', 'b'])->andReturn([$fakeDirectoryUser]);

        $this->commandTester->execute([
            'searchTerms' => ['a', 'b'],
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/abc\s+\| first last\s+\| preferredFirst\s+\| preferredLast\s+\| email\s+\| phone/',
            $output
        );
    }

    public function testTermRequired(): void
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([]);
    }
}
