<?php
namespace Tests\CliBundle\Command;

use Ilios\CliBundle\Command\UpdateFrontendCommand;
use Ilios\CliBundle\Command\UpdateServiceWorkerCommand;
use Ilios\CoreBundle\Service\Fetch;
use Ilios\CoreBundle\Service\Filesystem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem as SymfonyFileSystem;
use Mockery as m;

class UpdateServiceWorkerCommandTest extends TestCase
{
    const COMMAND_NAME = 'ilios:maintenance:update-serviceworker';

    /** @var CommandTester */
    protected $commandTester;

    protected $builder;
    protected $fakeTestFileDir;

    /** @var  m\Mock */
    protected $fetch;

    /** @var  m\Mock */
    protected $fs;


    public function setUp()
    {
        $fs = new SymfonyFileSystem();
        $this->fakeTestFileDir = __DIR__ . '/FakeTestFiles';
        if (!$fs->exists($this->fakeTestFileDir)) {
            $fs->mkdir($this->fakeTestFileDir);
        }

        $this->fetch = m::mock(Fetch::class);
        $this->fs = m::mock(Filesystem::class);
        $command = new UpdateServiceWorkerCommand($this->fetch, $this->fs, $this->fakeTestFileDir, 'prod');
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

        unset($this->fetch);
        unset($this->fs);
        m::close();
    }
    
    public function testExecute()
    {
        $this->fetch->shouldReceive('get')->with(UpdateServiceWorkerCommand::CDN_ASSET_DOMAIN . 'sw.js')
            ->once()->andReturn('SWJS');
        $this->fetch->shouldReceive('get')->with(UpdateServiceWorkerCommand::CDN_ASSET_DOMAIN . 'sw-registration.js')
            ->once()->andReturn('SWREGJS');

        $this->fs->shouldReceive('dumpFile')->once()->with(
            $this->fakeTestFileDir . UpdateServiceWorkerCommand::SWJS_CACHE_FILE_NAME,
            gzencode('SWJS')
        );
        $this->fs->shouldReceive('dumpFile')->once()->with(
            $this->fakeTestFileDir . UpdateServiceWorkerCommand::SW_REGISTRATION_CACHE_FILE_NAME,
            gzencode('SWREGJS')
        );
        
        $this->commandTester->execute(array(
            'command'      => self::COMMAND_NAME,
        ));

        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/Service worker updated successfully!/',
            $output
        );
    }
}
