<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\SendTestEmailCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * Class SendTestEmailCommandTest
 * @package App\Tests\Command
 * @group cli
 */
class SendTestEmailCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private const COMMAND_NAME = 'ilios:send-test-email';

    protected m\MockInterface $mailer;

    protected CommandTester $commandTester;

    public function setUp(): void
    {
        parent::setUp();
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $this->mailer = m::mock(MailerInterface::class);

        $command = new SendTestEmailCommand(
            $this->mailer
        );
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->mailer);
    }

    /**
     * @covers \App\Command\SendChangeAlertsCommand::execute
     */
    public function testExecute()
    {
        $this->mailer->shouldReceive('send')
            ->with(m::on(function (Email $message) {
                $to = array_map(fn(Address $address) => $address->getAddress(), $message->getTo());
                $from = array_map(fn(Address $address) => $address->getAddress(), $message->getFrom());
                $subject = $message->getSubject();
                $body = $message->getTextBody();

                if (!in_array('to@example.com', $to)) {
                    return false;
                }
                if (!in_array('from@example.com', $from)) {
                    return false;
                }
                if ($subject !== 'Ilios Test Email') {
                    return false;
                }
                if ($body !== 'This is a test email from your ilios system.') {
                    return false;
                }

                return true;
            }))
            ->once();

        $this->commandTester->execute([
            'to' => 'to@example.com',
            'from' => 'from@example.com',
        ]);
        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
    }
}
