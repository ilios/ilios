<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Command\ImportMeshUniverseCommand;
use App\Repository\MeshDescriptorRepository;
use App\Service\Index\Mesh;
use Ilios\MeSH\Model\Descriptor;
use Ilios\MeSH\Model\DescriptorSet;
use Ilios\MeSH\Parser;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class ImportMeshUniverseCommandTest
 * @package App\Tests\Command
 */
#[Group('cli')]
#[CoversClass(ImportMeshUniverseCommand::class)]
class ImportMeshUniverseCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $meshParser;
    protected m\MockInterface $descriptorRepository;
    protected m\MockInterface $meshIndex;
    protected CommandTester $commandTester;

    public function setUp(): void
    {
        parent::setUp();
        $this->meshParser = m::mock(Parser::class);
        $this->descriptorRepository = m::mock(MeshDescriptorRepository::class);
        $this->meshIndex = m::mock(Mesh::class);

        $command = new ImportMeshUniverseCommand($this->meshParser, $this->descriptorRepository, $this->meshIndex);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find($command->getName());
        $this->commandTester = new CommandTester($commandInApp);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->meshParser);
        unset($this->descriptorRepository);
        unset($this->meshIndex);
        unset($this->commandTester);
    }

    public function testNoArgs(): void
    {
        $this->mockHappyPath();
        $url = 'https://nlmpubs.nlm.nih.gov/projects/mesh/MESH_FILES/xmlmesh/desc2024.xml';
        $this->meshParser
            ->shouldReceive('parse')
            ->withArgs([$url])
            ->once()
            ->andReturn(new DescriptorSet());
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("Started MeSH universe import, this will take a while...", $output);
        $this->assertStringContainsString("1/4: Parsing MeSH XML retrieved from {$url}.", $output);
        $this->assertStringContainsString("2/4: Clearing database of existing MeSH data.", $output);
        $this->assertStringContainsString("3/4: Importing MeSH data into database.", $output);
        $this->assertStringContainsString("4/4: Flagging orphaned MeSH descriptors as deleted.", $output);
        $this->assertMatchesRegularExpression("/Finished MeSH universe import in \d+ seconds./", $output);
    }

    public function testGivenFilePath(): void
    {
        $this->mockHappyPath();

        $path = '/path/to/my/desc.xml';
        $this->meshParser
            ->shouldReceive('parse')
            ->withArgs([$path])
            ->once()
            ->andReturn(new DescriptorSet());
        $this->commandTester->execute(
            [
                '--path' => $path,
            ]
        );
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("1/4: Parsing MeSH XML retrieved from {$path}.", $output);
    }

    public function testGivenUrl(): void
    {
        $this->mockHappyPath();

        $url = 'http://localhost/desc.xml';
        $this->meshParser
            ->shouldReceive('parse')
            ->withArgs([$url])
            ->once()
            ->andReturn(new DescriptorSet());
        $this->commandTester->execute(
            [
                '--url' => $url,
            ]
        );
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("1/4: Parsing MeSH XML retrieved from {$url}.", $output);
    }

    public function testYear2023(): void
    {
        $this->mockHappyPath();

        $year = '2023';
        $url = "https://nlmpubs.nlm.nih.gov/projects/mesh/2023/xmlmesh/desc2023.xml";
        $this->meshParser
            ->shouldReceive('parse')
            ->withArgs([$url])
            ->once()
            ->andReturn(new DescriptorSet());
        $this->commandTester->execute(
            [
                '--year' => $year,
            ]
        );
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("1/4: Parsing MeSH XML retrieved from {$url}.", $output);
    }

    public function testYear2024(): void
    {
        $this->mockHappyPath();

        $year = '2024';
        $url = "https://nlmpubs.nlm.nih.gov/projects/mesh/MESH_FILES/xmlmesh/desc2024.xml";
        $this->meshParser
            ->shouldReceive('parse')
            ->withArgs([$url])
            ->once()
            ->andReturn(new DescriptorSet());
        $this->commandTester->execute(
            [
                '--year' => $year,
            ]
        );
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("1/4: Parsing MeSH XML retrieved from {$url}.", $output);
    }

    public function testInvalidGivenYear(): void
    {
        $year = '1906';
        $this->meshIndex->shouldReceive('isEnabled');

        $this->commandTester->execute(
            [
                '--year' => $year,
            ]
        );

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Given year must be one of: 2023, 2024.', $output);

        $this->descriptorRepository->shouldNotHaveReceived('clearExistingData');
        $this->descriptorRepository->shouldNotHaveReceived('findDTOsBy');
        $this->descriptorRepository->shouldNotHaveReceived('upsertMeshUniverse');
        $this->descriptorRepository->shouldNotHaveReceived('flagDescriptorsAsDeleted');
    }

    public function testIndexesResults(): void
    {
        $this->descriptorRepository->shouldReceive('clearExistingData')->once();
        $this->descriptorRepository->shouldReceive('findDTOsBy')->once()->andReturn([]);
        $this->descriptorRepository->shouldReceive('upsertMeshUniverse')->once();
        $this->descriptorRepository->shouldReceive('flagDescriptorsAsDeleted')->once();
        $this->meshIndex->shouldReceive('isEnabled')->andReturn(true);

        $descriptor = m::mock(Descriptor::class);
        $descriptorSet = m::mock(DescriptorSet::class);
        $descriptorSet->shouldReceive('getDescriptors')->once()->andReturn([$descriptor]);
        $descriptorSet->shouldReceive('getDescriptorUis')->andReturn(['id']);
        $this->meshIndex
            ->shouldReceive('index')->with([$descriptor])
            ->once()->andReturn(true);

        $url = 'https://nlmpubs.nlm.nih.gov/projects/mesh/MESH_FILES/xmlmesh/desc2024.xml';
        $this->meshParser
            ->shouldReceive('parse')
            ->withArgs([$url])
            ->once()
            ->andReturn($descriptorSet);
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("Started MeSH universe import, this will take a while...", $output);
        $this->assertStringContainsString("1/5: Parsing MeSH XML retrieved from {$url}.", $output);
        $this->assertStringContainsString("2/5: Clearing database of existing MeSH data.", $output);
        $this->assertStringContainsString("3/5: Importing MeSH data into database.", $output);
        $this->assertStringContainsString("4/5: Flagging orphaned MeSH descriptors as deleted.", $output);
        $this->assertStringContainsString("5/5: Adding MeSH data to the search index.", $output);
        $this->assertMatchesRegularExpression("/Finished MeSH universe import in \d+ seconds./", $output);
    }

    protected function mockHappyPath(): void
    {
        $this->descriptorRepository->shouldReceive('clearExistingData')->once();
        $this->descriptorRepository->shouldReceive('findDTOsBy')->once()->andReturn([]);
        $this->descriptorRepository->shouldReceive('upsertMeshUniverse')->once();
        $this->descriptorRepository->shouldReceive('flagDescriptorsAsDeleted')->once();
        $this->meshIndex->shouldReceive('isEnabled')->andReturn(false);
    }
}
