<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\ImportMeshUniverseCommand;
use App\Repository\MeshDescriptorRepository;
use App\Service\Index\Mesh;
use Ilios\MeSH\Model\Descriptor;
use Ilios\MeSH\Model\DescriptorSet;
use Ilios\MeSH\Parser;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class ImportMeshUniverseCommandTest
 * @package App\Tests\Command
 * @group cli
 * @coversDefaultClass \App\Command\ImportMeshUniverseCommand
 */
class ImportMeshUniverseCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var m\MockInterface
     */

    protected $meshParser;

    /**
     * @var m\MockInterface
     */
    protected $descriptorRepository;

    /**
     * @var Mesh|m\MockInterface
     */
    protected $meshIndex;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    /**
     * @var string
     */
    private const COMMAND_NAME = 'ilios:import-mesh-universe';

    /**
     * @inheritdoc
     */
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
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * @inheritdoc
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->meshParser);
        unset($this->descriptorRepository);
        unset($this->meshIndex);
        unset($this->commandTester);
    }

    /**
     * @covers ::execute
     */
    public function testNoArgs()
    {
        $this->mockHappyPath();
        $url = 'ftp://nlmpubs.nlm.nih.gov/online/mesh/MESH_FILES/xmlmesh/desc2021.xml';
        $this->meshParser
            ->shouldReceive('parse')
            ->withArgs([$url])
            ->once()
            ->andReturn(new DescriptorSet());
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("Started MeSH universe import, this will take a while...", $output);
        $this->assertStringContainsString("1/4: Parsing MeSH XML retrieved from ${url}.", $output);
        $this->assertStringContainsString("2/4: Clearing database of existing MeSH data.", $output);
        $this->assertStringContainsString("3/4: Importing MeSH data into database.", $output);
        $this->assertStringContainsString("4/4: Flagging orphaned MeSH descriptors as deleted.", $output);
        $this->assertMatchesRegularExpression("/Finished MeSH universe import in \d+ seconds./", $output);
    }

    /**
     * @covers ::execute
     */
    public function testGivenFilePath()
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
        $this->assertStringContainsString("1/4: Parsing MeSH XML retrieved from ${path}.", $output);
    }

    /**
     * @covers ::execute
     */
    public function testGivenUrl()
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
        $this->assertStringContainsString("1/4: Parsing MeSH XML retrieved from ${url}.", $output);
    }

    /**
     * @covers ::execute
     */
    public function testYear2020()
    {
        $this->mockHappyPath();

        $year = '2020';
        $url = "ftp://nlmpubs.nlm.nih.gov/online/mesh/MESH_FILES/xmlmesh/desc2020.xml";
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
        $this->assertStringContainsString("1/4: Parsing MeSH XML retrieved from ${url}.", $output);
    }

    /**
     * @covers ::execute
     */
    public function testYear2021()
    {
        $this->mockHappyPath();

        $year = '2021';
        $url = "ftp://nlmpubs.nlm.nih.gov/online/mesh/MESH_FILES/xmlmesh/desc2021.xml";
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
        $this->assertStringContainsString("1/4: Parsing MeSH XML retrieved from ${url}.", $output);
    }

    /**
     * @covers ::execute
     */
    public function testInvalidGivenYear()
    {
        $year = '1906';
        $this->expectExceptionMessage('Given year must be one of: 2020, 2021');
        $this->meshIndex->shouldReceive('isEnabled');

        $this->commandTester->execute(
            [
                '--year' => $year,
            ]
        );
        $this->descriptorRepository->shouldNotHaveReceived('clearExistingData');
        $this->descriptorRepository->shouldNotHaveReceived('findDTOsBy');
        $this->descriptorRepository->shouldNotHaveReceived('upsertMeshUniverse');
        $this->descriptorRepository->shouldNotHaveReceived('flagDescriptorsAsDeleted');
    }

    /**
     * @covers ::execute
     */
    public function testIndexesResults()
    {
        $this->descriptorRepository->shouldReceive('clearExistingData')->once();
        $this->descriptorRepository->shouldReceive('findDTOsBy')->once()->andReturn([]);
        $this->descriptorRepository->shouldReceive('upsertMeshUniverse')->once();
        $this->descriptorRepository->shouldReceive('flagDescriptorsAsDeleted')->once();
        $this->meshIndex->shouldReceive('isEnabled')->andReturn(true);

        $descriptor = m::mock(Descriptor::class);
        $descriptorSet = m::mock(DescriptorSet::class);

        $descriptorSet
            ->shouldReceive('getDescriptors')->once()->andReturn([$descriptor])
            ->shouldReceive('getDescriptorUis')->andReturn(['id']);
        $this->meshIndex
            ->shouldReceive('index')->with([$descriptor])
            ->once()->andReturn(true);

        $url = 'ftp://nlmpubs.nlm.nih.gov/online/mesh/MESH_FILES/xmlmesh/desc2021.xml';
        $this->meshParser
            ->shouldReceive('parse')
            ->withArgs([$url])
            ->once()
            ->andReturn($descriptorSet);
        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString("Started MeSH universe import, this will take a while...", $output);
        $this->assertStringContainsString("1/5: Parsing MeSH XML retrieved from ${url}.", $output);
        $this->assertStringContainsString("2/5: Clearing database of existing MeSH data.", $output);
        $this->assertStringContainsString("3/5: Importing MeSH data into database.", $output);
        $this->assertStringContainsString("4/5: Flagging orphaned MeSH descriptors as deleted.", $output);
        $this->assertStringContainsString("5/5: Adding MeSH data to the search index.", $output);
        $this->assertMatchesRegularExpression("/Finished MeSH universe import in \d+ seconds./", $output);
    }

    protected function mockHappyPath()
    {
        $this->descriptorRepository->shouldReceive('clearExistingData')->once();
        $this->descriptorRepository->shouldReceive('findDTOsBy')->once()->andReturn([]);
        $this->descriptorRepository->shouldReceive('upsertMeshUniverse')->once();
        $this->descriptorRepository->shouldReceive('flagDescriptorsAsDeleted')->once();
        $this->meshIndex->shouldReceive('isEnabled')->andReturn(false);
    }
}
