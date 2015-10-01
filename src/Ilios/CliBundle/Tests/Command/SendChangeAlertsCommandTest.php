<?php
namespace Ilios\CliBundle\Tests\Command;

use Ilios\CliBundle\Command\SendChangeAlertsCommand;
use Ilios\CoreBundle\Entity\Manager\AlertManagerInterface;
use Ilios\CoreBundle\Entity\Manager\AuditLogManagerInterface;
use Ilios\CoreBundle\Entity\Manager\OfferingManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Send Change Alerts command test.
 *
 * Class SendChangeAlertsCommandTest
 * @package Ilios\CliBundle\Tests\Command
 */
class SendChangeAlertsCommandTest extends KernelTestCase
{
    const COMMAND_NAME = 'ilios:messaging:send-change-alerts';

    /**
     * @var OfferingManagerInterface
     */
    protected $fakeOfferingManager;

    /**
     * @var AlertManagerInterface
     */
    protected $fakeAlertManager;

    /**
     * @var AuditLogManagerInterface
     */
    protected $fakeAuditLogManager;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    public function setUp()
    {
        $this->fakeOfferingManager = $this
            ->getMockBuilder('Ilios\CoreBundle\Entity\Manager\OfferingManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->fakeAlertManager = $this
            ->getMockBuilder('Ilios\CoreBundle\Entity\Manager\AlertManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->fakeAuditLogManager = $this
            ->getMockBuilder('Ilios\CoreBundle\Entity\Manager\AuditLogManager')
            ->disableOriginalConstructor()
            ->getMock();

        $kernel = $this->createKernel();
        $kernel->boot();
        $application = new Application($kernel);

        $command = new SendChangeAlertsCommand(
            $this->fakeAlertManager,
            $this->fakeAuditLogManager,
            $this->fakeOfferingManager,
            $kernel->getContainer()->get('templating'),
            $kernel->getContainer()->get('mailer')
        );
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        unset($this->fakeOfferingManager);
        unset($this->fakeAlertManager);
        unset($this->fakeAuditLogManager);
        m::close();
    }

    /**
     * @covers Ilios\CliBundle\Command\SendChangeAlertsCommand::execute
     */
    public function testExecuteDryRun()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
