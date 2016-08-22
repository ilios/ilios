<?php
namespace Tests\CliBundle\Command;

use Ilios\CliBundle\Command\UpdateFrontendCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;
use Mockery as m;

class UpdateFrontendCommandTest extends \PHPUnit_Framework_TestCase
{
    const COMMAND_NAME = 'ilios:maintenance:update-frontend';
    
    protected $commandTester;
    protected $builder;
    protected $fs;
    protected $fakeTestFileDir;

    public function setUp()
    {
        $fs = new SymfonyFileSystem();
        $this->fakeTestFileDir = __DIR__ . '/FakeTestFiles';
        if (!$fs->exists($this->fakeTestFileDir)) {
            $fs->mkdir($this->fakeTestFileDir);
        }

        $this->builder = m::mock('Ilios\WebBundle\Service\WebIndexFromJson');
        $this->fs = m::mock('Ilios\CoreBundle\Classes\Filesystem');
        $command = new UpdateFrontendCommand($this->builder, $this->fs, $this->fakeTestFileDir, 'blank', true);
        $application = new Application();
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        $fs = new SymfonyFileSystem();
        $fs->remove($this->fakeTestFileDir);

        unset($this->builder);
        unset($this->builder);
        unset($this->fs);
        m::close();
    }
    
    public function testExecute()
    {
        $this->builder->shouldReceive('getIndex')->once()->with('prod', null)->andReturn('index-string-thing');
        $this->fs->shouldReceive('dumpFile')->once()
            ->with($this->fakeTestFileDir . '/ilios/index.html', 'index-string-thing');
        
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
        ));

        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Frontend updated successfully!/',
            $output
        );
    }

    public function testExecuteStagingBuild()
    {
        $this->builder->shouldReceive('getIndex')->once()->with('stage', null)->andReturn('index-string-thing');
        $this->fs->shouldReceive('dumpFile')->once()
            ->with($this->fakeTestFileDir . '/ilios/index.html', 'index-string-thing');

        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            '--staging-build'         => true
        ));


        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Frontend updated successfully from staging build!/',
            $output
        );
    }

    public function testExecuteDevBuild()
    {
        $this->builder->shouldReceive('getIndex')->once()->with('dev', null)->andReturn('index-string-thing');
        $this->fs->shouldReceive('dumpFile')->once()
            ->with($this->fakeTestFileDir . '/ilios/index.html', 'index-string-thing');

        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            '--dev-build'         => true
        ));


        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Frontend updated successfully from dev build!/',
            $output
        );
    }

    public function testExecuteVersionBuild()
    {
        $this->builder->shouldReceive('getIndex')->once()->with('prod', 'foo.bar')->andReturn('index-string-thing');
        $this->fs->shouldReceive('dumpFile')->once()
            ->with($this->fakeTestFileDir . '/ilios/index.html', 'index-string-thing');

        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
            '--at-version'         => 'foo.bar'
        ));


        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Frontend updated successfully to version foo.bar!/',
            $output
        );
    }
}
