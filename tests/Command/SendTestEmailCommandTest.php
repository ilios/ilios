<?php
namespace App\Tests\Command;

use App\Command\SendTestEmailCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

class SendTestEmailCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:send-test-email';

    /**
     * @var m\Mock
     */
    protected $mailer;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    public function setUp()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $this->mailer = m::mock(\Swift_Mailer::class);

        $command = new SendTestEmailCommand(
            $this->mailer
        );
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown() : void
    {
        unset($this->mailer);
    }

    /**
     * @covers \App\Command\SendChangeAlertsCommand::execute
     *
     */
    public function testExecute()
    {
        $this->mailer->shouldReceive('send')
            ->with(m::on(function (\Swift_Message $message) {
                $to = array_keys($message->getTo());
                $from = array_keys($message->getFrom());
                $subject = $message->getSubject();
                $body = $message->getBody();

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
            }));

        $this->commandTester->execute([
            'to' => 'to@example.com',
            'from' => 'from@example.com',
        ]);
    }
}
