<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use App\Command\ListSchoolConfigValuesCommand;
use App\Entity\SchoolConfigInterface;
use App\Entity\SchoolInterface;
use App\Repository\SchoolConfigRepository;
use App\Repository\SchoolRepository;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class ListSchoolConfigValuesCommandTest
 * @package App\Tests\Command
 */
#[Group('cli')]
class ListSchoolConfigValuesCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected m\MockInterface $schoolRepository;
    protected m\MockInterface $schoolConfigRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->schoolRepository = m::mock(SchoolRepository::class);
        $this->schoolConfigRepository = m::mock(SchoolConfigRepository::class);
        $command = new ListSchoolConfigValuesCommand($this->schoolRepository, $this->schoolConfigRepository);
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
        unset($this->schoolRepository);
        unset($this->schoolConfigRepository);
        unset($this->commandTester);
    }

    public function testExecute(): void
    {
        $mockSchool = m::mock(SchoolInterface::class);
        $this->schoolRepository->shouldReceive('findOneBy')->once()->with(['id' => '1'])->andReturn($mockSchool);
        $mockConfig = m::mock(SchoolConfigInterface::class);
        $mockConfig->shouldReceive('getName')->once()->andReturn('the-name');
        $mockConfig->shouldReceive('getValue')->once()->andReturn('the-value');
        $this->schoolConfigRepository->shouldReceive('findBy')
            ->with(['school' => '1'], ['name' => 'asc'])
            ->once()
            ->andReturn([$mockConfig]);

        $this->commandTester->execute([
            'school' => '1',
        ]);
        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/\sthe-name\s|\sthe-value\s/',
            $output
        );
    }

    public function testSchoolRequired(): void
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([]);
    }
}
