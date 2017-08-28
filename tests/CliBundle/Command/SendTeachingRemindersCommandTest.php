<?php
namespace Tests\CliBundle\Command;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CliBundle\Command\SendTeachingRemindersCommand;
use Ilios\CoreBundle\Entity\Course;
use Ilios\CoreBundle\Entity\InstructorGroup;
use Ilios\CoreBundle\Entity\LearnerGroup;
use Ilios\CoreBundle\Entity\LearnerGroupInterface;
use Ilios\CoreBundle\Entity\Manager\OfferingManager;
use Ilios\CoreBundle\Entity\Objective;
use Ilios\CoreBundle\Entity\ObjectiveInterface;
use Ilios\CoreBundle\Entity\Offering;
use Ilios\CoreBundle\Entity\OfferingInterface;
use Ilios\CoreBundle\Entity\School;
use Ilios\CoreBundle\Entity\Session;
use Ilios\CoreBundle\Entity\SessionType;
use Ilios\CoreBundle\Entity\User;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Service\Config;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Send Teaching Reminder command test.
 *
 * Class SendTeachingRemindersCommandTest
 */
class SendTeachingRemindersCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    const COMMAND_NAME = 'ilios:messaging:send-teaching-reminders';

    /**
     * @var OfferingManager
     */
    protected $fakeOfferingManager;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    /**
     * @var string
     */
    protected $timezone;

    public function setUp()
    {
        $offering = $this->createOffering();

        $this->fakeOfferingManager = $this
            ->getMockBuilder('Ilios\CoreBundle\Entity\Manager\OfferingManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->fakeOfferingManager
            ->method('getOfferingsForTeachingReminders')
            ->will($this->returnValueMap(
                [
                    [ 7, new ArrayCollection([ $offering ]) ],
                    [ 10, new ArrayCollection() ],
                ]
            ));

        $kernel = $this->createKernel();
        $kernel->boot();
        $application = new Application($kernel);


        $this->timezone = 'UTC';
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('timezone')->andReturn($this->timezone);

        $command = new SendTeachingRemindersCommand(
            $this->fakeOfferingManager,
            $kernel->getContainer()->get('templating'),
            $kernel->getContainer()->get('mailer'),
            $config
        );
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        unset($this->fakeOfferingManager);
    }

    /**
     * @covers \Ilios\CliBundle\Command\SendTeachingRemindersCommand::execute
     */
    public function testExecuteDryRun()
    {
        $sender = 'foo@bar.edu';
        $baseUrl = 'https://ilios.bar.edu';

        $this->commandTester->execute([
            'sender' => $sender,
            'base_url' => $baseUrl,
            '--dry-run' => true,
        ]);

        /** @var OfferingInterface $offering */
        $offering = $this->fakeOfferingManager->getOfferingsForTeachingReminders(7)->toArray()[0];

        $output = $this->commandTester->getDisplay();

        /** @var UserInterface $instructor */
        foreach ($offering->getAllInstructors()->toArray() as $instructor) {
            $this->assertContains("To: {$instructor->getEmail()}", $output);
            $this->assertContains("Dear {$instructor->getFirstName()} {$instructor->getLastName()}", $output);
        }

        $timezone = new \DateTimeZone($this->timezone);
        $startDate = $offering->getStartDate()->setTimezone($timezone);
        $endDate = $offering->getEndDate()->setTimezone($timezone);

        $this->assertContains("From: {$sender}", $output);
        $subject = SendTeachingRemindersCommand::DEFAULT_MESSAGE_SUBJECT;
        $this->assertContains("Subject: {$subject}", $output);
        $this->assertContains("upcoming {$offering->getSession()->getSessionType()->getTitle()}", $output);
        $this->assertContains("School of {$offering->getSession()->getCourse()->getSchool()->getTitle()}", $output);
        $courseTitle = trim(strip_tags($offering->getSession()->getCourse()->getTitle()));
        $this->assertContains("Course:   {$courseTitle}", $output);
        $sessionTitle = trim(strip_tags($offering->getSession()->getTitle()));
        $this->assertContains("Session:  {$sessionTitle}", $output);
        $this->assertContains("Date:     {$startDate->format('D M d, Y')}", $output);
        $this->assertContains("Time:     {$startDate->format('h:i a')} - {$endDate->format('h:i a')}", $output);
        $this->assertContains("Location: {$offering->getRoom()}", $output);
        $this->assertContains(
            "Coordinator at {$offering->getSession()->getCourse()->getSchool()->getIliosAdministratorEmail()}.",
            $output
        );

        /** @var LearnerGroupInterface $learnerGroup */
        foreach ($offering->getLearnerGroups()->toArray() as $learnerGroup) {
            $this->assertContains("- {$learnerGroup->getTitle()}", $output);
        }

        /** @var UserInterface $learner */
        foreach ($offering->getLearners()->toArray() as $learner) {
            $this->assertContains("- {$learner->getFirstName()} {$learner->getLastName()}", $output);
        }

        /** @var ObjectiveInterface $objective */
        foreach ($offering->getSession()->getObjectives() as $objective) {
            $title = trim(strip_tags($objective->getTitle()));
            $this->assertContains("- {$title}", $output);
        }

        /** @var ObjectiveInterface $objective */
        foreach ($offering->getSession()->getCourse()->getObjectives() as $objective) {
            $title = trim(strip_tags($objective->getTitle()));
            $this->assertContains("- {$title}", $output);
        }

        $this->assertContains("{$baseUrl}/courses/{$offering->getSession()->getCourse()->getId()}", $output);

        $totalMailsSent = count($offering->getAllInstructors()->toArray());
        $this->assertContains("Sent {$totalMailsSent} teaching reminders.", $output);
    }

    /**
     * @covers \Ilios\CliBundle\Command\SendTeachingRemindersCommand::execute
     */
    public function testExecuteDryRunWithNoResult()
    {
        $sender = 'foo@bar.edu';
        $baseUrl = 'https://ilios.bar.edu';

        $this->commandTester->execute([
            'sender' => $sender,
            'base_url' => $baseUrl,
            '--dry-run' => true,
            '--days' => 10,
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertContains('No offerings with pending teaching reminders found.', $output);
    }

    /**
     * @covers \Ilios\CliBundle\Command\SendTeachingRemindersCommand::execute
     */
    public function testExecuteDryRunWithSenderName()
    {
        $sender = 'foo@bar.edu';
        $name = 'Horst Krause';
        $subject = "Custom email subject";
        $baseUrl = 'https://ilios.bar.edu';

        $this->commandTester->execute([
            'sender' => $sender,
            'base_url' => $baseUrl,
            '--dry-run' => true,
            '--subject' => $subject,
            '--sender_name' => $name,
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertContains("From: {$name} <{$sender}>", $output);
    }

    /**
     * @covers \Ilios\CliBundle\Command\SendTeachingRemindersCommand::execute
     */
    public function testExecuteDryRunWithCustomSubject()
    {
        $sender = 'foo@bar.edu';
        $subject = "Custom email subject";
        $baseUrl = 'https://ilios.bar.edu';

        $this->commandTester->execute([
            'sender' => $sender,
            'base_url' => $baseUrl,
            '--dry-run' => true,
            '--subject' => $subject,
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertContains("Subject: {$subject}", $output);
    }

    /**
     * @covers \Ilios\CliBundle\Command\SendTeachingRemindersCommand::execute
     */
    public function testExecute()
    {
        $sender = 'foo@bar.edu';
        $baseUrl = 'https://ilios.bar.edu';

        $this->commandTester->execute([
            'sender' => $sender,
            'base_url' => $baseUrl,
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertRegExp('/^Sent (\d+) teaching reminders\.$/', trim($output));
    }

    /**
     * @covers \Ilios\CliBundle\Command\SendTeachingRemindersCommand::execute
     */
    public function testExecuteWithMissingInput()
    {
        $this->expectException(\RuntimeException::class, 'Not enough arguments');
        $this->commandTester->execute([]);

        $this->expectException(\RuntimeException::class, 'Not enough arguments');
        $this->commandTester->execute([
            'sender' => 'foo@bar.com',
            'base_url' => null,
        ]);
    }

    /**
     * @covers \Ilios\CliBundle\Command\SendTeachingRemindersCommand::execute
     */
    public function testExecuteWithInvalidInput()
    {
        $this->commandTester->execute([
            'sender' => 'not an email',
            'base_url' => 'http://foobar.com',
            '--days' => -1
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertContains(
            "Invalid value '-1' for '--days' option. Must be greater or equal to 0.",
            $output
        );
        $this->assertContains(
            "Invalid value 'not an email' for '--sender' option. Must be a valid email address.",
            $output
        );
    }

    /**
     * @return OfferingInterface
     *
     * @todo This is truly in bad form. Refactor fixture loading out. [ST 2015/09/25]
     */
    protected function createOffering()
    {
        $school = new School();
        $school->setId(1);
        $school->setIliosAdministratorEmail('admin@testing.edu');
        $school->setTemplatePrefix('TEST');
        $school->setTitle('Testing');

        $course = new Course();
        $course->setId(1);
        $course->setTitle('Test Course <em>1</em>');
        $course->setSchool($school);

        $i = 0;
        foreach (['A', 'B', 'C'] as $letter) {
            $courseObjective = new Objective();
            $courseObjective->setId(++$i);
            $courseObjective->setTitle("Course <i>Objective</i> '{$letter}'");
            $course->addObjective($courseObjective);
        }

        $session = new Session();
        $session->setId(1);
        $session->setTitle('Test Session <b>1</b>');
        $session->setCourse($course);

        $sessionType = new SessionType();
        $sessionType->setId(1);
        $sessionType->setTitle('Session Type A');
        $session->setSessionType($sessionType);

        $i = 0;
        foreach (['A', 'B', 'C'] as $letter) {
            $sessionObjective = new Objective();
            $sessionObjective->setId(++$i);
            $sessionObjective->setTitle("Session Objective <strong>{$letter}</strong>");
            $session->addObjective($sessionObjective);
        }

        $instructor1 = new User();
        $instructor1->setId(1);
        $instructor1->setFirstName('Jane');
        $instructor1->setLastName('Doe');
        $instructor1->setEmail('jane.doe@test.com');

        $instructor2 = new User();
        $instructor2->setId(2);
        $instructor2->setFirstName("Jimmy");
        $instructor2->setLastName('Smith');
        $instructor2->setEmail('mike.smith@test.com');

        $instructorGroup = new InstructorGroup();
        $instructorGroup->setId(1);
        $instructorGroup->addUser($instructor2);

        $learnerGroup = new LearnerGroup();
        $learnerGroup->setId(1);
        $learnerGroup->setTitle("Learner Group 'alpha'");

        $learner = new User();
        $learner->setId(2);
        $learner->setFirstName("D'arcy");
        $learner->setLastName("O'Donovan");

        $offering = new Offering();
        $offering->setId(1);
        $offering->setStartDate(new \DateTime('2015-09-28 03:45:00', new \DateTimeZone('UTC')));
        $offering->setEndDate(new \DateTime('2015-09-28 05:45:00', new \DateTimeZone('UTC')));
        $offering->setSession($session);
        $offering->addInstructor($instructor1);
        $offering->addInstructorGroup($instructorGroup);
        $offering->addLearner($learner);
        $offering->addLearnerGroup($learnerGroup);
        $offering->setRoom('Library - Room 119');

        return $offering;
    }
}
