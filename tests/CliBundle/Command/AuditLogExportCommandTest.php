<?php

namespace Tests\CliBundle\Command;

use Ilios\CliBundle\Command\AuditLogExportCommand;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AuditLogExportCommandTest
 *
 * @link http://symfony.com/doc/current/components/console/introduction.html#testing-commands
 * @link http://symfony.com/doc/current/cookbook/console/console_command.html#testing-commands
 * @link http://www.ardianys.com/2013/04/symfony2-test-console-command-which-use.html
 */
class AuditLogExportCommandTest extends WebTestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->container = static::createClient()->getContainer();
        $this->loadFixtures([
            'Tests\CliBundle\Fixture\LoadAuditLogData',
        ]);

        $application = new Application(static::$kernel);
        $command = new AuditLogExportCommand();
        $application->add($command);

        $command = $application->find('ilios:maintenance:export-audit-log');
        $this->commandTester = new CommandTester($command);
    }

    /**
     * @covers \Ilios\CliBundle\Command\InstallFirstUserCommand::execute
     */
    public function testExecuteWithDefaultRange()
    {

        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertContains('YesterdaysEvent', $output);
        $this->assertContains('TodaysEvent', $output);
        $this->assertNotContains('LastYearsEvent', $output);
    }

    /**
     * @covers \Ilios\CliBundle\Command\InstallFirstUserCommand::execute
     */
    public function testExecuteWithCustomRange()
    {
        $this->commandTester->execute([
            'from' => '2 years ago',
            'to' => '2 days ago',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertNotContains('TodaysEvent', $output);
        $this->assertNotContains('YesterdaysEvent', $output);
        $this->assertContains('LastYearsEvent', $output);
    }

    /**
     * @covers \Ilios\CliBundle\Command\InstallFirstUserCommand::execute
     */
    public function testExecuteWithDeletion()
    {
        // set the delete flag
        $this->commandTester->execute([
            'from' => '2 days ago',
            'to' => 'now',
            '--delete' => true,
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertContains('YesterdaysEvent', $output);
        $this->assertContains('TodaysEvent', $output);

        // re-run date range - they should be gone now.
        $this->commandTester->execute([
            'from' => '2 days ago',
            'to' => 'now',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertNotContains('YesterdaysEvent', $output);
        $this->assertNotContains('TodaysEvent', $output);

        // last year's event should still be there tho.
        // running this just to verify that no overreach in deletion occurred.
        $this->commandTester->execute([
            'from' => '2 years ago',
            'to' => 'now',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertContains('LastYearsEvent', $output);
    }
}
