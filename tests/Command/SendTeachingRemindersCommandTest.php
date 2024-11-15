<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\CourseObjective;
use App\Entity\CourseObjectiveInterface;
use App\Command\SendTeachingRemindersCommand;
use App\Entity\Course;
use App\Entity\InstructorGroup;
use App\Entity\LearnerGroup;
use App\Entity\LearnerGroupInterface;
use App\Entity\Offering;
use App\Entity\OfferingInterface;
use App\Entity\School;
use App\Entity\Session;
use App\Entity\SessionObjective;
use App\Entity\SessionObjectiveInterface;
use App\Entity\SessionType;
use App\Entity\User;
use App\Entity\UserInterface;
use App\Repository\OfferingRepository;
use App\Repository\SchoolRepository;
use App\Service\Config;
use DateTime;
use DateTimeZone;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Mailer\MailerInterface;

/**
 * Send Teaching Reminder command test.
 *
 * Class SendTeachingRemindersCommandTest
 */
#[Group('cli')]
#[CoversClass(SendTeachingRemindersCommand::class)]
class SendTeachingRemindersCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $fakeOfferingRepository;
    protected m\MockInterface $fakeSchoolRepository;
    protected m\MockInterface $mailer;
    protected CommandTester $commandTester;
    protected string $timezone;
    protected m\MockInterface $fs;

    protected string $testDir;

    public function setUp(): void
    {
        parent::setUp();
        $this->fakeOfferingRepository = m::mock(OfferingRepository::class);
        $this->fakeSchoolRepository = m::mock(SchoolRepository::class);
        $this->mailer = m::mock(MailerInterface::class);
        $this->testDir = sys_get_temp_dir();
        $this->fs = m::mock(Filesystem::class);

        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $this->timezone = 'UTC';
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->with('timezone')->andReturn($this->timezone);

        $command = new SendTeachingRemindersCommand(
            $this->fakeOfferingRepository,
            $this->fakeSchoolRepository,
            static::getContainer()->get('twig'),
            $this->mailer,
            $config,
            $this->fs,
            $this->testDir
        );
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
        unset($this->fakeOfferingRepository);
        unset($this->fakeSchoolRepository);
        unset($this->fs);
        unset($this->mailer);
        unset($this->commandTester);
        unset($this->timezone);
    }

    public function testExecuteDryRun(): void
    {
        $sender = 'foo@bar.edu';
        $baseUrl = 'https://ilios.bar.edu';

        $offering = $this->createOffering();
        $this->fakeOfferingRepository->shouldReceive('getOfferingsForTeachingReminders')
            ->with(7, [1])->andReturn([$offering]);
        $this->fakeSchoolRepository->shouldReceive('getIds')->andReturn([1]);

        $this->fs->shouldReceive('exists')->with(
            $this->testDir . '/custom/templates/email/TEST_' . SendTeachingRemindersCommand::DEFAULT_TEMPLATE_NAME
        )->once()->andReturn(false);

        $this->commandTester->execute([
            'sender' => $sender,
            'base_url' => $baseUrl,
            '--dry-run' => true,
        ]);

        $output = $this->commandTester->getDisplay();

        /** @var UserInterface $instructor */
        foreach ($offering->getAllInstructors()->toArray() as $instructor) {
            $this->assertStringContainsString("To: {$instructor->getEmail()}", $output);
            $this->assertStringContainsString(
                "Dear {$instructor->getFirstName()} {$instructor->getLastName()}",
                $output
            );
        }

        $timezone = new DateTimeZone($this->timezone);
        $startDate = $offering->getStartDate()->setTimezone($timezone);
        $endDate = $offering->getEndDate()->setTimezone($timezone);

        $this->assertStringContainsString("From: {$sender}", $output);
        $subject = SendTeachingRemindersCommand::DEFAULT_MESSAGE_SUBJECT;
        $this->assertStringContainsString("Subject: {$subject}", $output);
        $this->assertStringContainsString("upcoming {$offering->getSession()->getSessionType()->getTitle()}", $output);
        $this->assertStringContainsString(
            "School of {$offering->getSession()->getCourse()->getSchool()->getTitle()}'s ",
            $output
        );
        $courseTitle = trim(strip_tags($offering->getSession()->getCourse()->getTitle()));
        $this->assertStringContainsString("Course:   {$courseTitle}", $output);
        $sessionTitle = trim(strip_tags($offering->getSession()->getTitle()));
        $this->assertStringContainsString("Session:  {$sessionTitle}", $output);
        $this->assertStringContainsString("Date:     {$startDate->format('D M d, Y')}", $output);
        $this->assertStringContainsString(
            "Time:     {$startDate->format('h:i a')} - {$endDate->format('h:i a')}",
            $output
        );
        $this->assertStringContainsString("Location: {$offering->getSite()} {$offering->getRoom()}", $output);
        $this->assertStringContainsString("Virtual Learning Link: {$offering->getUrl()}", $output);

        $this->assertStringContainsString(
            "Coordinator at {$offering->getSession()->getCourse()->getSchool()->getIliosAdministratorEmail()}.",
            $output
        );

        /** @var LearnerGroupInterface $learnerGroup */
        foreach ($offering->getLearnerGroups()->toArray() as $learnerGroup) {
            $this->assertStringContainsString("- {$learnerGroup->getTitle()}", $output);
        }

        /** @var UserInterface $learner */
        foreach ($offering->getLearners()->toArray() as $learner) {
            $this->assertStringContainsString("- {$learner->getFirstName()} {$learner->getLastName()}", $output);
        }

        $courseId = $offering->getSession()->getCourse()->getId();
        $sessionId  = $offering->getSession()->getId();
        $this->assertStringContainsString(
            "{$baseUrl}/courses/{$courseId}/sessions/{$sessionId}",
            $output
        );

        $totalMailsSent = count($offering->getAllInstructors()->toArray());
        $this->assertStringContainsString("Sent {$totalMailsSent} teaching reminders.", $output);
    }

    public function testExecuteDryRunWithNoResult(): void
    {
        $sender = 'foo@bar.edu';
        $baseUrl = 'https://ilios.bar.edu';
        $this->fakeOfferingRepository->shouldReceive('getOfferingsForTeachingReminders')
            ->with(10, [1])->andReturn([]);
        $this->fakeSchoolRepository->shouldReceive('getIds')->andReturn([1]);

        $this->commandTester->execute([
            'sender' => $sender,
            'base_url' => $baseUrl,
            '--dry-run' => true,
            '--days' => 10,
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertStringContainsString('No offerings with pending teaching reminders found.', $output);
    }

    public function testExecuteDryRunWithSenderName(): void
    {
        $sender = 'foo@bar.edu';
        $name = 'Horst Krause';
        $subject = "Custom email subject";
        $baseUrl = 'https://ilios.bar.edu';
        $offering = $this->createOffering();
        $this->fakeOfferingRepository->shouldReceive('getOfferingsForTeachingReminders')
            ->with(7, [1])->andReturn([$offering]);
        $this->fakeSchoolRepository->shouldReceive('getIds')->andReturn([1]);


        $this->fs->shouldReceive('exists')->with(
            $this->testDir . '/custom/templates/email/TEST_' . SendTeachingRemindersCommand::DEFAULT_TEMPLATE_NAME
        )->once()->andReturn(false);

        $this->commandTester->execute([
            'sender' => $sender,
            'base_url' => $baseUrl,
            '--dry-run' => true,
            '--subject' => $subject,
            '--sender_name' => $name,
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertStringContainsString("From: {$name} <{$sender}>", $output);
    }

    public function testExecuteDryRunWithPreferredEmail(): void
    {
        $sender = 'foo@bar.edu';
        $name = 'Horst Krause';
        $subject = "Custom email subject";
        $baseUrl = 'https://ilios.bar.edu';
        $offering = $this->createOffering();
        $this->fakeOfferingRepository->shouldReceive('getOfferingsForTeachingReminders')
            ->with(7, [1])->andReturn([$offering]);
        $this->fakeSchoolRepository->shouldReceive('getIds')->andReturn([1]);

        /** @var UserInterface $instructor */
        foreach ($offering->getAllInstructors()->toArray() as $instructor) {
            $instructor->setPreferredEmail(strrev($instructor->getEmail()));
        }

        $this->fs->shouldReceive('exists')->with(
            $this->testDir . '/custom/templates/email/TEST_' . SendTeachingRemindersCommand::DEFAULT_TEMPLATE_NAME
        )->once()->andReturn(false);

        $this->commandTester->execute([
            'sender' => $sender,
            'base_url' => $baseUrl,
            '--dry-run' => true,
            '--subject' => $subject,
            '--sender_name' => $name,
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertStringContainsString("From: {$name} <{$sender}>", $output);

        /** @var UserInterface $instructor */
        foreach ($offering->getAllInstructors()->toArray() as $instructor) {
            $this->assertStringContainsString("To: {$instructor->getPreferredEmail()}", $output);
            $this->assertStringNotContainsString("To: {$instructor->getEmail()}", $output);
        }
    }

    public function testExecuteDryRunWithCustomSubject(): void
    {
        $sender = 'foo@bar.edu';
        $subject = "Custom email subject";
        $baseUrl = 'https://ilios.bar.edu';
        $offering = $this->createOffering();
        $this->fakeOfferingRepository->shouldReceive('getOfferingsForTeachingReminders')
            ->with(7, [1])->andReturn([$offering]);
        $this->fakeSchoolRepository->shouldReceive('getIds')->andReturn([1]);

        $this->fs->shouldReceive('exists')->with(
            $this->testDir . '/custom/templates/email/TEST_' . SendTeachingRemindersCommand::DEFAULT_TEMPLATE_NAME
        )->once()->andReturn(false);

        $this->commandTester->execute([
            'sender' => $sender,
            'base_url' => $baseUrl,
            '--dry-run' => true,
            '--subject' => $subject,
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertStringContainsString("Subject: {$subject}", $output);
    }

    /**
     * @link https://github.com/ilios/ilios/issues/1975
     */
    public function testExecuteSchoolTitleEndsWithS(): void
    {
        $offering = $this->createOffering();
        $this->fakeOfferingRepository->shouldReceive('getOfferingsForTeachingReminders')
            ->with(7, [1])->andReturn([$offering]);
        $this->fakeSchoolRepository->shouldReceive('getIds')->andReturn([1]);
        $schoolTitle = 'Global Health Sciences';
        $offering->getSession()->getCourse()->getSchool()->setTitle($schoolTitle);

        $sender = 'foo@bar.edu';
        $baseUrl = 'https://ilios.bar.edu';

        $this->fs->shouldReceive('exists')->with(
            $this->testDir . '/custom/templates/email/TEST_' . SendTeachingRemindersCommand::DEFAULT_TEMPLATE_NAME
        )->once()->andReturn(false);

        $this->commandTester->execute([
            'sender' => $sender,
            'base_url' => $baseUrl,
            '--dry-run' => true,
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertStringContainsString(
            "School of {$offering->getSession()->getCourse()->getSchool()->getTitle()}' ",
            $output
        );
    }

    public function testExecute(): void
    {
        $sender = 'foo@bar.edu';
        $baseUrl = 'https://ilios.bar.edu';
        $offering = $this->createOffering();
        $this->fakeOfferingRepository->shouldReceive('getOfferingsForTeachingReminders')
            ->with(7, [1])->andReturn([$offering]);
        $this->fakeSchoolRepository->shouldReceive('getIds')->andReturn([1]);

        $this->fs->shouldReceive('exists')->with(
            $this->testDir . '/custom/templates/email/TEST_' . SendTeachingRemindersCommand::DEFAULT_TEMPLATE_NAME
        )->once()->andReturn(false);

        $this->mailer->shouldReceive('send')->twice();

        $this->commandTester->execute([
            'sender' => $sender,
            'base_url' => $baseUrl,
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertMatchesRegularExpression('/^Sent 2 teaching reminders\.$/', trim($output));
    }

    public function testExecuteDryRunWithSchools(): void
    {
        $sender = 'foo@bar.edu';
        $baseUrl = 'https://ilios.bar.edu';

        $offering = $this->createOffering();
        $this->fakeOfferingRepository->shouldReceive('getOfferingsForTeachingReminders')
            ->with(7, [1])->andReturn([$offering]);
        $this->fakeSchoolRepository->shouldReceive('getIds')->andReturn([1]);

        $this->fs->shouldReceive('exists')->with(
            $this->testDir . '/custom/templates/email/TEST_' . SendTeachingRemindersCommand::DEFAULT_TEMPLATE_NAME
        )->once()->andReturn(false);

        $this->commandTester->execute([
            'sender' => $sender,
            'base_url' => $baseUrl,
            '--dry-run' => true,
            '--schools' => '1',
        ]);

        $output = $this->commandTester->getDisplay();
        $totalMailsSent = count($offering->getAllInstructors()->toArray());
        $this->assertStringContainsString("Sent {$totalMailsSent} teaching reminders.", $output);
    }

    public function testExecuteDryRunWithSchoolsWithNoOfferings(): void
    {
        $sender = 'foo@bar.edu';
        $baseUrl = 'https://ilios.bar.edu';

        $this->fakeOfferingRepository->shouldReceive('getOfferingsForTeachingReminders')
            ->with(7, [2])->andReturn([]);

        $this->commandTester->execute([
            'sender' => $sender,
            'base_url' => $baseUrl,
            '--dry-run' => true,
            '--schools' => '2',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("No offerings with pending teaching reminders found.", $output);
    }

    public function testExecuteWithMissingInput(): void
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([]);

        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([
            'sender' => 'foo@bar.com',
            'base_url' => null,
        ]);
    }

    public function testExecuteWithInvalidInput(): void
    {
        $this->commandTester->execute([
            'sender' => 'not an email',
            'base_url' => 'http://foobar.com',
            '--days' => -1,
        ]);

        $output = $this->commandTester->getDisplay();

        $this->assertStringContainsString(
            "Invalid value '-1' for '--days' option. Must be greater or equal to 0.",
            $output
        );
        $this->assertStringContainsString(
            "Invalid value 'not an email' for '--sender' option. Must be a valid email address.",
            $output
        );
    }

    public function testExecuteNoVirtualLearningLink(): void
    {
        $sender = 'foo@bar.edu';
        $baseUrl = 'https://ilios.bar.edu';

        $offering = $this->createOffering();
        $offering->setUrl(null);
        $this->fakeOfferingRepository->shouldReceive('getOfferingsForTeachingReminders')
            ->with(7, [1])->andReturn([$offering]);
        $this->fakeSchoolRepository->shouldReceive('getIds')->andReturn([1]);

        $this->fs->shouldReceive('exists')->with(
            $this->testDir . '/custom/templates/email/TEST_' . SendTeachingRemindersCommand::DEFAULT_TEMPLATE_NAME
        )->once()->andReturn(false);

        $this->commandTester->execute([
            'sender' => $sender,
            'base_url' => $baseUrl,
            '--dry-run' => true,
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertStringNotContainsString("Virtual Learning Link:", $output);
    }

    /**
     * @todo This is truly in bad form. Refactor fixture loading out. [ST 2015/09/25]
     */
    protected function createOffering(): OfferingInterface
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
            $courseObjective = new CourseObjective();
            $courseObjective->setId(++$i);
            $courseObjective->setTitle("Course <i>Objective</i> '{$letter}'");
            $course->addCourseObjective($courseObjective);
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
            $sessionObjective = new SessionObjective();
            $sessionObjective->setId(++$i);
            $sessionObjective->setTitle("Session Objective <strong>{$letter}</strong>");
            $session->addSessionObjective($sessionObjective);
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
        $offering->setStartDate(new DateTime('2015-09-28 03:45:00', new DateTimeZone('UTC')));
        $offering->setEndDate(new DateTime('2015-09-28 05:45:00', new DateTimeZone('UTC')));
        $offering->setSession($session);
        $offering->addInstructor($instructor1);
        $offering->addInstructorGroup($instructorGroup);
        $offering->addLearner($learner);
        $offering->addLearnerGroup($learnerGroup);
        $offering->setRoom('Library - Room 119');
        $offering->setUrl('https://iliosproject.org');

        return $offering;
    }
}
