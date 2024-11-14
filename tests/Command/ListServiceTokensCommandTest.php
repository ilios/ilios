<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\ListServiceTokensCommand;
use App\Entity\ServiceTokenInterface;
use App\Repository\ServiceTokenRepository;
use DateInterval;
use DateTime;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * @package App\Tests\Command
 * @group cli
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Command\ListServiceTokensCommand::class)]
class ListServiceTokensCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected m\MockInterface $serviceTokenRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->serviceTokenRepository = m::mock(ServiceTokenRepository::class);
        $command = new ListServiceTokensCommand($this->serviceTokenRepository);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find($command->getName());
        $this->commandTester = new CommandTester($commandInApp);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->commandTester);
        unset($this->serviceTokenRepository);
    }

    public function testDefaultsWithResults(): void
    {
        $createdAt1 = new DateTime('2015-01-30 00:00:00');
        $createdAt2 = new DateTime('2016-02-10 00:00:00');
        $expiresAt1 = new DateTime('2015-05-01 00:00:00');
        $expiresAt2 = new DateTime('2016-11-05 00:00:00');
        $serviceToken1 = m::mock(ServiceTokenInterface::class);
        $serviceToken2 = m::mock(ServiceTokenInterface::class);
        $serviceToken1->shouldReceive('getId')->andReturn(1);
        $serviceToken2->shouldReceive('getId')->andReturn(2);
        $serviceToken1->shouldReceive('isEnabled')->andReturn(true);
        $serviceToken2->shouldReceive('isEnabled')->andReturn(false);
        $serviceToken1->shouldReceive('getDescription')->andReturn('lorem');
        $serviceToken2->shouldReceive('getDescription')->andReturn('ipsum');
        $serviceToken1->shouldReceive('getCreatedAt')->andReturn($createdAt1);
        $serviceToken2->shouldReceive('getCreatedAt')->andReturn($createdAt2);
        $serviceToken1->shouldReceive('getExpiresAt')->andReturn($expiresAt1);
        $serviceToken2->shouldReceive('getExpiresAt')->andReturn($expiresAt2);
        $this->serviceTokenRepository->shouldReceive('findBy')->andReturn([
            $serviceToken1, $serviceToken2,
        ]);
        $this->commandTester->execute([]);
        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
        $output = trim($this->commandTester->getDisplay());
        $this->assertStringStartsWith('Service Tokens', $output);
        $this->assertStringContainsString(
            '| id | description | status   | created at                | expires at                |',
            $output
        );
        $this->assertStringContainsString(
            '| 1  | lorem       | enabled  | 2015-01-30T00:00:00+00:00 | 2015-05-01T00:00:00+00:00 |',
            $output
        );
        $this->assertStringContainsString(
            '| 2  | ipsum       | disabled | 2016-02-10T00:00:00+00:00 | 2016-11-05T00:00:00+00:00 |',
            $output
        );
    }

    public function testNoTokensFound(): void
    {
        $this->serviceTokenRepository->shouldReceive('findBy')->andReturn([]);
        $this->commandTester->execute([]);
        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
        $output = trim($this->commandTester->getDisplay());
        $this->assertEquals("Service Tokens\n\nNo tokens found.", $output);
    }

    public function testExcludeDisabledTokens(): void
    {
        $this->serviceTokenRepository->shouldReceive('findBy')->withArgs([['enabled' => true]])->andReturn([]);
        $this->commandTester->execute([
            '--' . ListServiceTokensCommand::EXCLUDE_DISABLED_KEY => true,
        ]);
        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
        $output = trim($this->commandTester->getDisplay());
        $this->assertStringContainsString('- excludes disabled tokens', $output);
    }

    public function testExcludeExpiredTokens(): void
    {
        $expiredDate = (new DateTime())->sub(new DateInterval('PT8H'));
        $futureDate = (new DateTime())->add(new DateInterval('PT8H'));
        $serviceToken1 = m::mock(ServiceTokenInterface::class);
        $serviceToken2 = m::mock(ServiceTokenInterface::class);
        $serviceToken1->shouldReceive('getId')->andReturn(1);
        $serviceToken2->shouldNotReceive('getId');
        $serviceToken1->shouldReceive('isEnabled')->andReturn(true);
        $serviceToken2->shouldNotReceive('isEnabled');
        $serviceToken1->shouldReceive('getDescription')->andReturn('description');
        $serviceToken2->shouldNotReceive('getDescription');
        $serviceToken1->shouldReceive('getCreatedAt')->andReturn($expiredDate);
        $serviceToken2->shouldNotReceive('getCreatedAt');
        $serviceToken1->shouldReceive('getExpiresAt')->andReturn($futureDate);
        $serviceToken2->shouldReceive('getExpiresAt')->andReturn($expiredDate);

        $this->serviceTokenRepository
            ->shouldReceive('findBy')
            ->andReturn([$serviceToken1, $serviceToken2]);
        $this->commandTester->execute([
            '--' . ListServiceTokensCommand::EXCLUDE_EXPIRED_KEY => true,
        ]);
        $output = trim($this->commandTester->getDisplay());
        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('- excludes expired tokens', $output);
        $this->assertStringContainsString(
            "| 1  | description | enabled | {$expiredDate->format('c')} | {$futureDate->format('c')} |",
            $output
        );
    }

    public function testExcludeExpiresWithin(): void
    {
        $expiresWithin = 'P30D';
        $createdAtDate = new DateTime();
        $expiresWithinDate = (new DateTime())->add(new DateInterval('P20D'));
        $expiresNotWithinDate = (new DateTime())->add(new DateInterval($expiresWithin))->add(new DateInterval('PT8H'));
        $serviceToken1 = m::mock(ServiceTokenInterface::class);
        $serviceToken2 = m::mock(ServiceTokenInterface::class);
        $serviceToken1->shouldReceive('getId')->andReturn(1);
        $serviceToken2->shouldNotReceive('getId');
        $serviceToken1->shouldReceive('isEnabled')->andReturn(true);
        $serviceToken2->shouldNotReceive('isEnabled');
        $serviceToken1->shouldReceive('getDescription')->andReturn('description');
        $serviceToken2->shouldNotReceive('getDescription');
        $serviceToken1->shouldReceive('getCreatedAt')->andReturn($createdAtDate);
        $serviceToken2->shouldNotReceive('getCreatedAt');
        $serviceToken1->shouldReceive('getExpiresAt')->andReturn($expiresWithinDate);
        $serviceToken2->shouldReceive('getExpiresAt')->andReturn($expiresNotWithinDate);

        $this->serviceTokenRepository
            ->shouldReceive('findBy')
            ->andReturn([$serviceToken1, $serviceToken2]);
        $this->commandTester->execute([
            '--' . ListServiceTokensCommand::EXPIRES_WITHIN_KEY => $expiresWithin,
        ]);
        $output = trim($this->commandTester->getDisplay());
        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('- excludes tokens with an expiration date exceeding', $output);
        $this->assertStringContainsString(
            "| 1  | description | enabled | {$createdAtDate->format('c')} | {$expiresWithinDate->format('c')} |",
            $output
        );
    }
}
