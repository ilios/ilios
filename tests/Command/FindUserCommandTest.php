<?php

declare(strict_types=1);

namespace App\Tests\Command;

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
 * @group cli
 */
class FindUserCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    private const COMMAND_NAME = 'ilios:find-user';

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
        $commandInApp = $application->find(self::COMMAND_NAME);
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
            'email' => 'email',
            'telephoneNumber' => 'phone',
            'campusId' => 'abc',
        ];
        $this->directory->shouldReceive('find')->with(['a', 'b'])->andReturn([$fakeDirectoryUser]);

        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'searchTerms'         => ['a', 'b']
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/abc\s+\| first\s+\| last\s+\| email\s+\| phone/',
            $output
        );
    }

    public function testTermRequired(): void
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute(['command' => self::COMMAND_NAME]);
    }
}
