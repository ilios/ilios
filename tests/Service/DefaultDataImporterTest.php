<?php

declare(strict_types=1);

namespace App\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use App\Repository\DataImportRepositoryInterface;
use App\Service\DefaultDataImporter;
use App\Service\DefaultDataLoader;
use App\Tests\TestCase;
use Mockery as m;

/**
 * @package App\Tests\Service
 */
#[CoversClass(DefaultDataImporter::class)]
class DefaultDataImporterTest extends TestCase
{
    protected m\MockInterface $repository;
    protected m\MockInterface $loader;
    protected DefaultDataImporter $importer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = m::spy(DataImportRepositoryInterface::class);
        $this->loader = m::mock(DefaultDataLoader::class);
        $this->importer = new DefaultDataImporter($this->loader);
    }

    protected function tearDown(): void
    {
        unset($this->importer);
        unset($this->loader);
        unset($this->repository);
        parent::tearDown();
    }

    public function testImportAamcMethod(): void
    {
        $input = [
            ['AM001', 'Clinical Documentation Review', '1'],
            ['AM015', 'Practical (Lab)', '0'],
        ];

        $output = [
            ['AM001', 'Clinical Documentation Review', true],
            ['AM015', 'Practical (Lab)', false],
        ];

        $this->loader->shouldReceive('load')->withArgs([DefaultDataImporter::AAMC_METHOD])->andReturn($input);
        $this->importer->import($this->repository, DefaultDataImporter::AAMC_METHOD, []);
        foreach ($output as $out) {
            $this->repository->shouldHaveReceived('import')->withArgs(function (...$args) use ($out) {
                $type = $args[1] === DefaultDataImporter::AAMC_METHOD;
                $data = $args[0] === $out;

                return $type && $data;
            })->once();
        }
    }

    public function testImportAamcPcrs(): void
    {
        $input = [
            ['aamc-pcrs-comp-c0599', 'Other professionalism'],
            ['aamc-pcrs-comp-c0899', 'Other personal and professional development'],
        ];

        $output = $input;

        $this->loader->shouldReceive('load')->withArgs([DefaultDataImporter::AAMC_PCRS])->andReturn($input);
        $this->importer->import($this->repository, DefaultDataImporter::AAMC_PCRS, []);
        foreach ($output as $out) {
            $this->repository->shouldHaveReceived('import')->withArgs(function (...$args) use ($out) {
                $type = $args[1] === DefaultDataImporter::AAMC_PCRS;
                $data = $args[0] === $out;

                return $type && $data;
            })->once();
        }
    }

    public function testImportAamcResourceType(): void
    {
        $input = [
            ['RE010', 'Film/Video', 'A camera-based recording of visual and audible components...'],
            ['RE018', 'Written or Visual Media (or Digital Equivalent)', 'Reference materials produced...'],
        ];

        $output = $input;

        $this->loader->shouldReceive('load')->withArgs([DefaultDataImporter::AAMC_RESOURCE_TYPE])->andReturn($input);
        $this->importer->import($this->repository, DefaultDataImporter::AAMC_RESOURCE_TYPE, []);
        foreach ($output as $out) {
            $this->repository->shouldHaveReceived('import')->withArgs(function (...$args) use ($out) {
                $type = $args[1] === DefaultDataImporter::AAMC_RESOURCE_TYPE;
                $data = $args[0] === $out;

                return $type && $data;
            })->once();
        }
    }

    public function testImportAlertChangeType(): void
    {
        $input = [
            ['1', 'Time'],
            ['2', 'Location'],
        ];

        $output = [
            [1, 'Time'],
            [2, 'Location'],
        ];

        $this->loader->shouldReceive('load')->withArgs([DefaultDataImporter::ALERT_CHANGE_TYPE])->andReturn($input);
        $this->importer->import($this->repository, DefaultDataImporter::ALERT_CHANGE_TYPE, []);
        foreach ($output as $out) {
            $this->repository->shouldHaveReceived('import')->withArgs(function (...$args) use ($out) {
                $type = $args[1] === DefaultDataImporter::ALERT_CHANGE_TYPE;
                $data = $args[0] === $out;

                return $type && $data;
            })->once();
        }
    }

    public function testImportApplicationConfig(): void
    {
        $input = [
            ['1', 'Time'],
            ['2', 'Location'],
        ];

        $output = [
            [1, 'Time'],
            [2, 'Location'],
        ];

        $this->loader->shouldReceive('load')->withArgs([DefaultDataImporter::ALERT_CHANGE_TYPE])->andReturn($input);
        $this->importer->import($this->repository, DefaultDataImporter::ALERT_CHANGE_TYPE, []);
        foreach ($output as $out) {
            $this->repository->shouldHaveReceived('import')->withArgs(function (...$args) use ($out) {
                $type = $args[1] === DefaultDataImporter::ALERT_CHANGE_TYPE;
                $data = $args[0] === $out;

                return $type && $data;
            })->once();
        }
    }

    public function testImportAssessmentOption(): void
    {
        $input = [
            ['2', 'formative'],
            ['1', 'summative'],
        ];

        $output = [
            [2, 'formative'],
            [1, 'summative'],
        ];

        $this->loader->shouldReceive('load')->withArgs([DefaultDataImporter::ASSESSMENT_OPTION])->andReturn($input);
        $this->importer->import($this->repository, DefaultDataImporter::ASSESSMENT_OPTION, []);
        foreach ($output as $out) {
            $this->repository->shouldHaveReceived('import')->withArgs(function (...$args) use ($out) {
                $type = $args[1] === DefaultDataImporter::ASSESSMENT_OPTION;
                $data = $args[0] === $out;

                return $type && $data;
            })->once();
        }
    }

    public function testImportCompetency(): void
    {
        $input = [
            ['1', 'Patient Care', null, '1', '1'],
            ['12', 'Patient Management', '1', '1', '0'],
        ];

        $output = [
            [1, 'Patient Care', null, '1', true],
            [12, 'Patient Management', '1', '1', false],
        ];

        $this->loader->shouldReceive('load')->withArgs([DefaultDataImporter::COMPETENCY])->andReturn($input);
        $this->importer->import($this->repository, DefaultDataImporter::COMPETENCY, []);
        foreach ($output as $out) {
            $this->repository->shouldHaveReceived('import')->withArgs(function (...$args) use ($out) {
                $type = $args[1] === DefaultDataImporter::COMPETENCY;
                $data = $args[0] === $out;

                return $type && $data;
            })->once();
        }
    }

    public function testImportCourseClerkshipType(): void
    {
        $input = [
            ['1', 'block'],
            ['2', 'longitudinal'],
        ];

        $output = [
            [1, 'block'],
            [2, 'longitudinal'],
        ];

        $this->loader->shouldReceive('load')->withArgs([DefaultDataImporter::COURSE_CLERKSHIP_TYPE])->andReturn($input);
        $this->importer->import($this->repository, DefaultDataImporter::COURSE_CLERKSHIP_TYPE, []);
        foreach ($output as $out) {
            $this->repository->shouldHaveReceived('import')->withArgs(function (...$args) use ($out) {
                $type = $args[1] === DefaultDataImporter::COURSE_CLERKSHIP_TYPE;
                $data = $args[0] === $out;

                return $type && $data;
            })->once();
        }
    }

    public function testImportCurriculumInventoryInstitution(): void
    {
        $input = [
            ['1', 'School of Medicine', '000x1', 'Main Street', 'Anytown', 'XY', '12345', 'ZZ', '1'],
            ['2', 'School of Dentistry', '000x2', 'Broadway', 'Big City', 'YZ', '11111', 'XX', '2'],
        ];

        $output = [
            ['1', 'School of Medicine', '000x1', 'Main Street', 'Anytown', 'XY', '12345', 'ZZ', 1],
            ['2', 'School of Dentistry', '000x2', 'Broadway', 'Big City', 'YZ', '11111', 'XX', 2],
        ];

        $this->loader->shouldReceive('load')->withArgs([DefaultDataImporter::CURRICULUM_INVENTORY_INSTITUTION])
            ->andReturn($input);
        $this->importer->import($this->repository, DefaultDataImporter::CURRICULUM_INVENTORY_INSTITUTION, []);
        foreach ($output as $out) {
            $this->repository->shouldHaveReceived('import')->withArgs(function (...$args) use ($out) {
                $type = $args[1] === DefaultDataImporter::CURRICULUM_INVENTORY_INSTITUTION;
                $data = $args[0] === $out;

                return $type && $data;
            })->once();
        }
    }

    public function testImportLearningMaterialStatus(): void
    {
        $input = [
            ['1', 'Draft'],
            ['2', 'Final'],
        ];

        $output = [
            [1, 'Draft'],
            [2, 'Final'],
        ];

        $this->loader->shouldReceive('load')->withArgs([DefaultDataImporter::LEARNING_MATERIAL_STATUS])->andReturn(
            $input
        );
        $this->importer->import($this->repository, DefaultDataImporter::LEARNING_MATERIAL_STATUS, []);
        foreach ($output as $out) {
            $this->repository->shouldHaveReceived('import')->withArgs(function (...$args) use ($out) {
                $type = $args[1] === DefaultDataImporter::LEARNING_MATERIAL_STATUS;
                $data = $args[0] === $out;

                return $type && $data;
            })->once();
        }
    }

    public function testImportLearningMaterialUserRole(): void
    {
        $input = [
            ['1', 'Instructional Designer'],
            ['2', 'Author'],
        ];

        $output = [
            [1, 'Instructional Designer'],
            [2, 'Author'],
        ];

        $this->loader->shouldReceive('load')->withArgs([DefaultDataImporter::LEARNING_MATERIAL_USER_ROLE])->andReturn(
            $input
        );
        $this->importer->import($this->repository, DefaultDataImporter::LEARNING_MATERIAL_USER_ROLE, []);
        foreach ($output as $out) {
            $this->repository->shouldHaveReceived('import')->withArgs(function (...$args) use ($out) {
                $type = $args[1] === DefaultDataImporter::LEARNING_MATERIAL_USER_ROLE;
                $data = $args[0] === $out;

                return $type && $data;
            })->once();
        }
    }

    public function testImportSchool(): void
    {
        $input = [
            ['1', 'SOM', 'Medicine', 'ilios_admin@bogus.som.edu', 'ilios_change_alerts@bogus.som.edu'],
            ['2', 'SOD', 'Dentistry', 'ilios_admin@bogus.sod.edu', 'ilios_change_alerts@bogus.sod.edu'],
        ];

        $output = [
            [1, 'SOM', 'Medicine', 'ilios_admin@bogus.som.edu', 'ilios_change_alerts@bogus.som.edu'],
            [2, 'SOD', 'Dentistry', 'ilios_admin@bogus.sod.edu', 'ilios_change_alerts@bogus.sod.edu'],
        ];

        $this->loader->shouldReceive('load')->withArgs([DefaultDataImporter::SCHOOL])->andReturn($input);
        $this->importer->import($this->repository, DefaultDataImporter::SCHOOL, []);
        foreach ($output as $out) {
            $this->repository->shouldHaveReceived('import')->withArgs(function (...$args) use ($out) {
                $type = $args[1] === DefaultDataImporter::SCHOOL;
                $data = $args[0] === $out;

                return $type && $data;
            })->once();
        }
    }

    public function testImportSessionType(): void
    {
        $input = [
            ['1', 'Case-Based Instruction/Learning', '1', 'fff', '0', '1', '0'],
            ['39', 'Clinical Documentation Review', '1', 'c79376', '1', '1', '1'],
        ];

        $output = [
            [1, 'Case-Based Instruction/Learning', '1', 'fff', false, '1', false],
            [39, 'Clinical Documentation Review', '1', 'c79376', true, '1', true],
        ];

        $this->loader->shouldReceive('load')->withArgs([DefaultDataImporter::SESSION_TYPE])->andReturn($input);
        $this->importer->import($this->repository, DefaultDataImporter::SESSION_TYPE, []);
        foreach ($output as $out) {
            $this->repository->shouldHaveReceived('import')->withArgs(function (...$args) use ($out) {
                $type = $args[1] === DefaultDataImporter::SESSION_TYPE;
                $data = $args[0] === $out;

                return $type && $data;
            })->once();
        }
    }

    public function testImportTerm(): void
    {
        $input = [
            ['1', 'Administrative', null, 'Lorem', '1', '1'],
            ['2', 'Anatomy', null, 'Ipsum', '2', '0'],
        ];

        $output = [
            [1, 'Administrative', null, 'Lorem', '1', true],
            [2, 'Anatomy', null, 'Ipsum', '2', false],
        ];

        $this->loader->shouldReceive('load')->withArgs([DefaultDataImporter::TERM])->andReturn($input);
        $this->importer->import($this->repository, DefaultDataImporter::TERM, []);
        foreach ($output as $out) {
            $this->repository->shouldHaveReceived('import')->withArgs(function (...$args) use ($out) {
                $type = $args[1] === DefaultDataImporter::TERM;
                $data = $args[0] === $out;

                return $type && $data;
            })->once();
        }
    }

    public function testImportUserRole(): void
    {
        $input = [
            ['1', 'Course Director'],
            ['2', 'Developer'],
        ];

        $output = [
            [1, 'Course Director'],
            [2, 'Developer'],
        ];

        $this->loader->shouldReceive('load')->withArgs([DefaultDataImporter::USER_ROLE])->andReturn($input);
        $this->importer->import($this->repository, DefaultDataImporter::USER_ROLE, []);
        foreach ($output as $out) {
            $this->repository->shouldHaveReceived('import')->withArgs(function (...$args) use ($out) {
                $type = $args[1] === DefaultDataImporter::USER_ROLE;
                $data = $args[0] === $out;

                return $type && $data;
            })->once();
        }
    }

    public function testImportVocabulary(): void
    {
        $input = [
            ['2', 'Topics', '3', '1'],
            ['3', 'Resource Types', '1', '0'],
        ];

        $output = [
            [2, 'Topics', '3', true],
            [3, 'Resource Types', '1', false],
        ];

        $this->loader->shouldReceive('load')->withArgs([DefaultDataImporter::VOCABULARY])->andReturn($input);
        $this->importer->import($this->repository, DefaultDataImporter::VOCABULARY, []);
        foreach ($output as $out) {
            $this->repository->shouldHaveReceived('import')->withArgs(function (...$args) use ($out) {
                $type = $args[1] === DefaultDataImporter::VOCABULARY;
                $data = $args[0] === $out;

                return $type && $data;
            })->once();
        }
    }

    public function testImportCompetencyToAamcPcrsMapping(): void
    {
        $input = [
            ['7', 'aamc-pcrs-comp-c0101'],
            ['8', 'aamc-pcrs-comp-c0102'],
        ];

        $output = $input;

        $this->loader->shouldReceive('load')->withArgs([DefaultDataImporter::COMPETENCY_X_AAMC_PCRS])
            ->andReturn($input);
        $this->importer->import($this->repository, DefaultDataImporter::COMPETENCY_X_AAMC_PCRS, []);
        foreach ($output as $out) {
            $this->repository->shouldHaveReceived('import')->withArgs(function (...$args) use ($out) {
                $type = $args[1] === DefaultDataImporter::COMPETENCY_X_AAMC_PCRS;
                $data = $args[0] === $out;

                return $type && $data;
            })->once();
        }
    }

    public function testImportSessionTypeToAamcMethodMapping(): void
    {
        $input = [
            ['1', 'IM001'],
            ['2', 'IM013'],
        ];

        $output = $input;

        $this->loader->shouldReceive('load')->withArgs([DefaultDataImporter::SESSION_TYPE_X_AAMC_METHOD])->andReturn(
            $input
        );
        $this->importer->import($this->repository, DefaultDataImporter::SESSION_TYPE_X_AAMC_METHOD, []);
        foreach ($output as $out) {
            $this->repository->shouldHaveReceived('import')->withArgs(function (...$args) use ($out) {
                $type = $args[1] === DefaultDataImporter::SESSION_TYPE_X_AAMC_METHOD;
                $data = $args[0] === $out;

                return $type && $data;
            })->once();
        }
    }

    public function testImportTermToAamcResourceTypeMapping(): void
    {
        $input = [
            ['136', 'RE001'],
            ['137', 'RE002'],
        ];

        $output = $input;

        $this->loader->shouldReceive('load')->withArgs([DefaultDataImporter::TERM_X_AAMC_RESOURCE_TYPE])->andReturn(
            $input
        );
        $this->importer->import($this->repository, DefaultDataImporter::TERM_X_AAMC_RESOURCE_TYPE, []);
        foreach ($output as $out) {
            $this->repository->shouldHaveReceived('import')->withArgs(function (...$args) use ($out) {
                $type = $args[1] === DefaultDataImporter::TERM_X_AAMC_RESOURCE_TYPE;
                $data = $args[0] === $out;

                return $type && $data;
            })->once();
        }
    }
}
