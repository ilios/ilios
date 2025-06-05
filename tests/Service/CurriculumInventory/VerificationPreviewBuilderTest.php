<?php

declare(strict_types=1);

namespace App\Tests\Service\CurriculumInventory;

use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\Course;
use App\Entity\CourseClerkshipType;
use App\Entity\CurriculumInventoryAcademicLevel;
use App\Entity\CurriculumInventoryReport;
use App\Entity\CurriculumInventorySequenceBlock;
use App\Entity\DTO\AamcMethodDTO;
use App\Entity\DTO\AamcPcrsDTO;
use App\Repository\AamcMethodRepository;
use App\Repository\AamcPcrsRepository;
use App\Service\CurriculumInventory\Export\Aggregator;
use App\Service\CurriculumInventory\VerificationPreviewBuilder;
use App\Tests\TestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery;
use Mockery\MockInterface;

/**
 * Class VerificationPreviewBuilderTest
 * @package App\Tests\Service\CurriculumInventory
 */
#[CoversClass(VerificationPreviewBuilder::class)]
final class VerificationPreviewBuilderTest extends TestCase
{
    protected VerificationPreviewBuilder $builder;
    protected MockInterface $methodRepository;
    protected MockInterface $pcrsManager;
    protected MockInterface $aggregator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->aggregator = Mockery::mock(Aggregator::class);
        $this->methodRepository = Mockery::mock(AamcMethodRepository::class);
        $this->pcrsManager = Mockery::mock(AamcPcrsRepository::class);
        $this->builder = new VerificationPreviewBuilder(
            $this->aggregator,
            $this->methodRepository,
            $this->pcrsManager
        );

        $this->methodRepository->shouldReceive('findDTOsBy')->andReturn(
            [
                new AamcMethodDTO("AM001", "Clinical Documentation Review", true),
                new AamcMethodDTO("AM002", "Clinical Performance Rating/Checklist", true),
                new AamcMethodDTO("AM003", "Exam - Institutionally Developed, Clinical Performance", true),
                new AamcMethodDTO("AM004", "Exam - Institutionally Developed, Written/ Computer-based", true),
                new AamcMethodDTO("AM005", "Exam - Institutionally Developed, Oral", true),
                new AamcMethodDTO("AM006", "Exam - Licensure, Clinical Performance", true),
                new AamcMethodDTO("AM007", "Exam - Licensure, Written/Computer-based", true),
                new AamcMethodDTO("AM008", "Exam - Nationally Normed/Standardized, Subject", true),
                new AamcMethodDTO("AM009", "Multisource Assessment", true),
                new AamcMethodDTO("AM010", "Narrative Assessment", true),
                new AamcMethodDTO("AM011", "Oral Patient Presentation", true),
                new AamcMethodDTO("AM012", "Participation", true),
                new AamcMethodDTO("AM013", "Peer Assessment", true),
                new AamcMethodDTO("AM014", "Portfolio-Based Assessment", true),
                new AamcMethodDTO("AM015", "Practical (Lab)", false),
                new AamcMethodDTO("AM016", "Research or Project Assessment", true),
                new AamcMethodDTO("AM017", "Self-Assessment", true),
                new AamcMethodDTO("AM018", "Stimulated Recall", true),
                new AamcMethodDTO("AM019", "Exam – Institutionally Developed, Laboratory Practical (Lab)", true),
                new AamcMethodDTO("IM001", "Case-Based Instruction/Learning", true),
                new AamcMethodDTO("IM002", "Clinical Experience - Ambulatory", true),
                new AamcMethodDTO("IM003", "Clinical Experience - Inpatient", true),
                new AamcMethodDTO("IM004", "Concept Mapping", true),
                new AamcMethodDTO("IM005", "Conference", true),
                new AamcMethodDTO("IM006", "Demonstration", true),
                new AamcMethodDTO("IM007", "Discussion, Large Group (>12)", true),
                new AamcMethodDTO("IM008", "Discussion, Small Group (?12)", true),
                new AamcMethodDTO("IM009", "Games", true),
                new AamcMethodDTO("IM010", "Independent Learning", true),
                new AamcMethodDTO("IM011", "Journal Club", true),
                new AamcMethodDTO("IM012", "Laboratory", true),
                new AamcMethodDTO("IM013", "Lecture", true),
                new AamcMethodDTO("IM014", "Mentorship", true),
                new AamcMethodDTO("IM015", "Patient Presentation - Faculty", true),
                new AamcMethodDTO("IM016", "Patient Presentation - Learner", true),
                new AamcMethodDTO("IM017", "Peer Teaching", true),
                new AamcMethodDTO("IM018", "Preceptorship", true),
                new AamcMethodDTO("IM019", "Problem-Based Learning (PBL)", true),
                new AamcMethodDTO("IM020", "Reflection", true),
                new AamcMethodDTO("IM021", "Research", true),
                new AamcMethodDTO("IM022", "Role Play/Dramatization", true),
                new AamcMethodDTO("IM023", "Self-Directed Learning", true),
                new AamcMethodDTO("IM024", "Service Learning Activity", true),
                new AamcMethodDTO("IM025", "Simulation", true),
                new AamcMethodDTO("IM026", "Team-Based Learning (TBL)", true),
                new AamcMethodDTO("IM027", "Team-Building", true),
                new AamcMethodDTO("IM028", "Tutorial", true),
                new AamcMethodDTO("IM029", "Ward Rounds", true),
                new AamcMethodDTO("IM030", "Workshop Assessment", true),
                new AamcMethodDTO("IM031", "Patient Presentation - Patient", true),
            ]
        );

        $this->pcrsManager->shouldReceive("findDTOsBy")->andReturn(
            [
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0101",
                    "Perform all medical, diagnostic, and surgical procedures considered essential "
                    . "for the area of practice"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0102",
                    "Gather essential and accurate information about patients and their conditions through "
                    . "history-taking, physical examination, and the use of laboratory data, imaging and other tests"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0103",
                    "Organize and prioritize responsibilities to provide care that is safe, effective, "
                    . "and efficient"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0104",
                    "Interpret laboratory data, imaging studies, and other tests required for the area of practice"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0105",
                    "Make informed decisions about diagnostic and therapeutic interventions based on patient "
                    . "information and preferences, up-to-date scientific evidence, and clinical judgment"
                ),
                new AamcPcrsDTO("aamc-pcrs-comp-c0106", "Develop and carry out patient management plans"),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0107",
                    "Counsel and educate patients and their families to empower them to participate in their "
                    . "care and enable shared decision making"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0108",
                    "Provide appropriate referral of patients including ensuring continuity of care throughout "
                    . "transitions between providers or settings, and following up on patient progress and outcomes"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0109",
                    "Provide health care services to patients, families, and communities aimed at preventing "
                    . "health problems"
                ),
                new AamcPcrsDTO("aamc-pcrs-comp-c0199", "Other patient care"),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0201",
                    "Demonstrate an investigatory and analytic approach to clinical situations"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0202",
                    "Apply established and emerging bio-physical scientific principles fundamental to health "
                    . "care for patients and populations"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0203",
                    "Apply established and emerging principles of clinical sciences to diagnostic and therapeutic"
                    . "decision-making, clinical problem-solving, and other aspects of evidence-based health care"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0204",
                    "Apply principles of epidemiological sciences to the identification of health problems, risk "
                    . "factors, treatment strategies, resources, and disease prevention/health promotion efforts for "
                    . "patients and populations"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0205",
                    "Apply principles of social-behavioral sciences to provision of patient care, including "
                    . "assessment of the impact of psychosocial and cultural influences on health, disease, "
                    . "care-seeking, care compliance, and barriers to and attitudes toward care"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0206",
                    "Contribute to the creation, dissemination, application, and translation of new health "
                    . "care knowledge and practices"
                ),
                new AamcPcrsDTO("aamc-pcrs-comp-c0299", "Other knowledge for practice"),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0301",
                    "Identify strengths, deficiencies, and limits in one’s knowledge and expertise"
                ),
                new AamcPcrsDTO("aamc-pcrs-comp-c0302", "Set learning and improvement goals"),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0303",
                    "Identify and perform learning activities that address one’s gaps in knowledge, skills "
                    . "and/or attitudes"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0304",
                    "Systematically analyze practice using quality improvement methods, and implement changes "
                    . "with the goal of practice improvement"
                ),
                new AamcPcrsDTO("aamc-pcrs-comp-c0305", "Incorporate feedback into daily practice"),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0306",
                    "Locate, appraise, and assimilate evidence from scientific studies related to patients’ "
                    . "health problems"
                ),
                new AamcPcrsDTO("aamc-pcrs-comp-c0307", "Use information technology to optimize learning"),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0308",
                    "Participate in the education of patients, families, students, trainees, peers and other "
                    . "health professionals"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0309",
                    "Obtain and utilize information about individual patients, populations of patients, or "
                    . "communities from which patients are drawn to improve care"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0310",
                    "Continually identify, analyze, and implement new knowledge, guidelines, standards, "
                    . "technologies, products, or services that have been demonstrated to improve outcomes"
                ),
                new AamcPcrsDTO("aamc-pcrs-comp-c0399", "Other practice-based learning and improvement"),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0401",
                    "Communicate effectively with patients, families, and the public, as appropriate, across "
                    . "a broad range of socioeconomic and cultural backgrounds"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0402",
                    "Communicate effectively with colleagues within one's profession or specialty, other "
                    . "health professionals, and health related agencies (see also 7.3)"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0403",
                    "Work effectively with others as a member or leader of a health care team or other professional "
                    . "group (see also 7.4)"
                ),
                new AamcPcrsDTO("aamc-pcrs-comp-c0404", "Act in a consultative role to other health professionals"),
                new AamcPcrsDTO("aamc-pcrs-comp-c0405", "Maintain comprehensive, timely, and legible medical records"),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0406",
                    "Demonstrate sensitivity, honesty, and compassion in difficult conversations, including "
                    . "those about death, end of life, adverse events, bad news, disclosure of errors, and other "
                    . "sensitive topics"
                ),
                new AamcPcrsDTO("aamc-pcrs-comp-c0499", "Other interpersonal and communication skills"),
                new AamcPcrsDTO("aamc-pcrs-comp-c0501", "Demonstrate compassion, integrity, and respect for others"),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0502",
                    "Demonstrate responsiveness to patient needs that supersedes self-interest"
                ),
                new AamcPcrsDTO("aamc-pcrs-comp-c0503", "Demonstrate respect for patient privacy and autonomy"),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0504",
                    "Demonstrate accountability to patients, society, and the profession"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0505",
                    "Demonstrate sensitivity and responsiveness to a diverse patient population, including but not "
                    . "limited to diversity in gender, age, culture, race, religion, disabilities, and sexual "
                    . "orientation"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0506",
                    "Demonstrate a commitment to ethical principles pertaining to provision or withholding of care,"
                    . " confidentiality, informed consent, and business practices, including compliance with "
                    . "relevant laws, policies, and regulations"
                ),
                new AamcPcrsDTO("aamc-pcrs-comp-c0599", "Other professionalism"),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0601",
                    "Work effectively in various health care delivery settings and systems relevant to one's clinical 
                    " . "specialty"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0602",
                    "Coordinate patient care within the health care system relevant to one's clinical specialty"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0603",
                    "Incorporate considerations of cost awareness and risk-benefit analysis in patient and/or "
                    . "population-based care"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0604",
                    "Advocate for quality patient care and optimal patient care systems"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0605",
                    "Participate in identifying system errors and implementing potential systems solutions"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0606",
                    "Perform administrative and practice management responsibilities commensurate with one’s role, "
                    . "abilities, and qualifications"
                ),
                new AamcPcrsDTO("aamc-pcrs-comp-c0699", "Other systems-based practice"),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0701",
                    "Work with other health professionals to establish and maintain a climate of mutual respect, 
                    " . "dignity, diversity, ethical integrity, and trust"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0702",
                    "Use the knowledge of one’s own role and the roles of other health professionals to appropriately "
                    . "assess and address the health care needs of the patients and populations served"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0703",
                    "Communicate with other health professionals in a responsive and responsible manner that supports "
                    . "the maintenance of health and the treatment of disease in individual patients and populations"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0704",
                    "Participate in different team roles to establish, develop, and continuously enhance "
                    . "interprofessional teams to provide patient- and population-centered care that is safe, timely, "
                    . "efficient, effective, and equitable"
                ),
                new AamcPcrsDTO("aamc-pcrs-comp-c0799", "Other interprofessional collaboration"),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0801",
                    "Develop the ability to use self-awareness of knowledge, skills and emotional limitations "
                    . "to engage in appropriate help-seeking behaviors"
                ),
                new AamcPcrsDTO("aamc-pcrs-comp-c0802", "Demonstrate healthy coping mechanisms to respond to stress"),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0803",
                    "Manage conflict between personal and professional responsibilities"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0804",
                    "Practice flexibility and maturity in adjusting to change with the capacity to alter one's behavior"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0805",
                    "Demonstrate trustworthiness that makes colleagues feel secure when one is responsible for the "
                    . "care of patients"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0806",
                    "Provide leadership skills that enhance team functioning, the learning environment, and/or the 
                    " . "health care delivery system"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0807",
                    "Demonstrate self-confidence that puts patients, families, and members of the health care team "
                    . "at ease"
                ),
                new AamcPcrsDTO(
                    "aamc-pcrs-comp-c0808",
                    "Recognize that ambiguity is part of clinical health care and respond by utilizing appropriate "
                    . "resources in dealing with uncertainty"
                ),
                new AamcPcrsDTO("aamc-pcrs-comp-c0899", "Other personal and professional development"),
            ]
        );
    }

    protected function tearDown(): void
    {
        unset($this->aggregator);
        unset($this->methodRepository);
        unset($this->pcrsManager);
        unset($this->builder);
        parent::tearDown();
    }

    public function testGetAllEventsWithAssessmentsTaggedAsFormativeOrSummative(): void
    {
        $data['events'] = [
            ['method_id' => 'IM002'],
            ['method_id' => 'AM002', 'assessment_option_name' => 'formative'],
            ['method_id' => 'AM002', 'assessment_option_name' => 'formative'],
            ['method_id' => 'AM002', 'assessment_option_name' => 'formative'],
            ['method_id' => 'AM003', 'assessment_option_name' => 'summative'],
            ['method_id' => 'AM003', 'assessment_option_name' => 'formative'],
            ['method_id' => 'AM007', 'assessment_option_name' => 'summative'],
            ['method_id' => 'AM007', 'assessment_option_name' => 'summative'],
        ];
        $rows = $this->builder->getAllEventsWithAssessmentsTaggedAsFormativeOrSummative($data);
        $this->assertCount(3, $rows);
        $this->assertEquals([
            'id' => 'AM002',
            'title' => 'Clinical Performance Rating/Checklist',
            'num_summative_assessments' => 0,
            'num_formative_assessments' => 3,
        ], $rows[0]);
        $this->assertEquals([
            'id' => 'AM003',
            'title' => 'Exam - Institutionally Developed, Clinical Performance',
            'num_summative_assessments' => 1,
            'num_formative_assessments' => 1,
        ], $rows[1]);
        $this->assertEquals([
            'id' => 'AM007',
            'title' => 'Exam - Licensure, Written/Computer-based',
            'num_summative_assessments' => 2,
            'num_formative_assessments' => 0,
        ], $rows[2]);
    }

    public function testGetAllResourceTypes(): void
    {
        $data['events'] = [
            [],
            ['resource_types' => [
                ['resource_type_id' => 1, 'resource_type_title' => 'Foo'],
                ['resource_type_id' => 2, 'resource_type_title' => 'Bar'],
            ]],
            ['resource_types' => [
                ['resource_type_id' => 3, 'resource_type_title' => 'Baz'],
                ['resource_type_id' => 2, 'resource_type_title' => 'Bar'],
            ]],
            ['resource_types' => [
                ['resource_type_id' => 3, 'resource_type_title' => 'Baz'],
            ]],
            ['resource_types' => [
                ['resource_type_id' => 3, 'resource_type_title' => 'Baz'],
            ]],
        ];

        $rows = $this->builder->getAllResourceTypes($data);
        $this->assertCount(3, $rows);
        $this->assertEquals(['id' => 1, 'title' => 'Foo', 'count' => 1], $rows[0]);
        $this->assertEquals(['id' => 2, 'title' => 'Bar', 'count' => 2], $rows[1]);
        $this->assertEquals(['id' => 3, 'title' => 'Baz', 'count' => 3], $rows[2]);
    }

    public function testGetClerkshipSequenceBlockAssessmentMethods(): void
    {
        $data = [];

        $level1 = new CurriculumInventoryAcademicLevel();
        $level1->setLevel(1);
        $level2 = new CurriculumInventoryAcademicLevel();
        $level2->setLevel(2);
        $level3 = new CurriculumInventoryAcademicLevel();
        $level3->setLevel(3);

        $sequenceBlock1 = new CurriculumInventorySequenceBlock();
        $sequenceBlock1->setId(1);
        $sequenceBlock1->setTitle('Zeppelin Clerkship Year 2');
        $sequenceBlock1->setStartingAcademicLevel($level2);
        $sequenceBlock1->setEndingAcademicLevel($level3);
        $course1 = new Course();
        $course1->setClerkshipType(new CourseClerkshipType());
        $sequenceBlock1->setCourse($course1);

        $sequenceBlock2 = new CurriculumInventorySequenceBlock();
        $sequenceBlock2->setId(2);
        $sequenceBlock2->setTitle('Zeppelin Clerkship Year 1');
        $sequenceBlock2->setStartingAcademicLevel($level1);
        $sequenceBlock2->setEndingAcademicLevel($level2);
        $course2 = new Course();
        $course2->setClerkshipType(new CourseClerkshipType());
        $sequenceBlock2->setCourse($course2);

        $sequenceBlock3 = new CurriculumInventorySequenceBlock();
        $sequenceBlock3->setId(3);
        $sequenceBlock3->setTitle('Aardvark Clerkship Year 2');
        $sequenceBlock3->setStartingAcademicLevel($level2);
        $sequenceBlock3->setEndingAcademicLevel($level3);
        $course3 = new Course();
        $course3->setClerkshipType(new CourseClerkshipType());
        $sequenceBlock3->setCourse($course3);

        $sequenceBlock4 = new CurriculumInventorySequenceBlock();
        $sequenceBlock4->setId(4);
        $sequenceBlock4->setTitle('Non-clerkship');
        $course4 = new Course();
        $sequenceBlock4->setCourse($course4);

        $report = new CurriculumInventoryReport();
        $report->setSequenceBlocks(
            new ArrayCollection([
                $sequenceBlock1, $sequenceBlock2, $sequenceBlock3, $sequenceBlock4,
            ])
        );

        $data['report'] = $report;
        $data['events'] = [
            1 => ['event_id' => 1, 'method_id' => 'AM010', 'assessment_option_name' => 'formative'],
            2 => ['event_id' => 2, 'method_id' => 'AM013', 'assessment_option_name' => 'formative'],
            3 => ['event_id' => 3, 'method_id' => 'AM004', 'assessment_option_name' => 'summative'],
            4 => ['event_id' => 4, 'method_id' => 'AM004', 'assessment_option_name' => 'formative'],
            5 => ['event_id' => 5, 'method_id' => 'AM008', 'assessment_option_name' => 'summative'],
            6 => ['event_id' => 6, 'method_id' => 'AM003', 'assessment_option_name' => 'summative'],
            7 => ['event_id' => 7, 'method_id' => 'AM019', 'assessment_option_name' => 'formative'],
            8 => ['event_id' => 8, 'method_id' => 'AM016', 'assessment_option_name' => 'summative'],
        ];
        $data['sequence_block_references']['events'] = [
            1 => [
                ['id' => 1, 'event_id' => 1],
                ['id' => 1, 'event_id' => 2],
            ],
            2 => [
                ['id' => 2, 'event_id' => 3],
                ['id' => 2, 'event_id' => 4],
                ['id' => 2, 'event_id' => 5],
            ],
            3 => [
                ['id' => 3, 'event_id' => 6],
                ['id' => 3, 'event_id' => 7],
                ['id' => 3, 'event_id' => 8],
            ],
        ];

        $rhett = $this->builder->getClerkshipSequenceBlockAssessmentMethods($data);

        $methods = $rhett['methods'];
        $rows = $rhett['rows'];
        $this->assertCount(6, $methods);
        $this->assertEquals([
            'Faculty / resident rating',
            'Internal written exams',
            'NBME subject exams',
            'OSCE / SP exam',
            'Oral Exam or Pres.',
            'Other',
        ], $methods);
        $this->assertEquals([
            'title' => 'Zeppelin Clerkship Year 1',
            'starting_level' => 1,
            'ending_level' => 2,
            'methods' => [
                'Faculty / resident rating' => false,
                'Internal written exams' => true,
                'NBME subject exams' => true,
                'OSCE / SP exam' => false,
                'Oral Exam or Pres.' => false,
                'Other' => false,
            ],
            'num_exams' => 2,
            'has_formative_assessments' => true,
            'has_narrative_assessments' => false,
        ], $rows[0]);

        $this->assertEquals([
            'title' => 'Aardvark Clerkship Year 2',
            'starting_level' => 2,
            'ending_level' => 3,
            'methods' => [
                'Faculty / resident rating' => false,
                'Internal written exams' => false,
                'NBME subject exams' => false,
                'OSCE / SP exam' => true,
                'Oral Exam or Pres.' => false,
                'Other' => true,
            ],
            'num_exams' => 2,
            'has_formative_assessments' => true,
            'has_narrative_assessments' => false,
        ], $rows[1]);

        $this->assertEquals([
            'title' => 'Zeppelin Clerkship Year 2',
            'starting_level' => 2,
            'ending_level' => 3,
            'methods' => [
                'Faculty / resident rating' => true,
                'Internal written exams' => false,
                'NBME subject exams' => false,
                'OSCE / SP exam' => false,
                'Oral Exam or Pres.' => false,
                'Other' => true,
            ],
            'num_exams' => 0,
            'has_formative_assessments' => true,
            'has_narrative_assessments' => true,
        ], $rows[2]);
    }

    public function testGetClerkshipSequenceBlockInstructionalTime(): void
    {
        $data = [];

        $level1 = new CurriculumInventoryAcademicLevel();
        $level1->setLevel(1);
        $level2 = new CurriculumInventoryAcademicLevel();
        $level2->setLevel(2);
        $level3 = new CurriculumInventoryAcademicLevel();
        $level3->setLevel(3);

        $sequenceBlock1 = new CurriculumInventorySequenceBlock();
        $sequenceBlock1->setId(1);
        $sequenceBlock1->setDuration(30);
        $sequenceBlock1->setTitle('Zeppelin Clerkship Year 2');
        $sequenceBlock1->setStartingAcademicLevel($level2);
        $sequenceBlock1->setEndingAcademicLevel($level3);
        $course1 = new Course();
        $course1->setClerkshipType(new CourseClerkshipType());
        $sequenceBlock1->setCourse($course1);

        $sequenceBlock2 = new CurriculumInventorySequenceBlock();
        $sequenceBlock2->setId(2);
        $sequenceBlock2->setDuration(5);
        $sequenceBlock2->setTitle('Zeppelin Clerkship Year 1');
        $sequenceBlock2->setStartingAcademicLevel($level1);
        $sequenceBlock2->setEndingAcademicLevel($level2);
        $course2 = new Course();
        $course2->setClerkshipType(new CourseClerkshipType());
        $sequenceBlock2->setCourse($course2);

        $sequenceBlock3 = new CurriculumInventorySequenceBlock();
        $sequenceBlock3->setId(3);
        $sequenceBlock3->setDuration(7);
        $sequenceBlock3->setTitle('Aardvark Clerkship Year 2');
        $sequenceBlock3->setStartingAcademicLevel($level2);
        $sequenceBlock3->setEndingAcademicLevel($level3);
        $course3 = new Course();
        $course3->setClerkshipType(new CourseClerkshipType());
        $sequenceBlock3->setCourse($course3);

        $sequenceBlock4 = new CurriculumInventorySequenceBlock();
        $sequenceBlock4->setId(4);
        $sequenceBlock4->setDuration(5);
        $sequenceBlock4->setTitle('Non-Clerkship');
        $course4 = new Course();
        $sequenceBlock4->setCourse($course4);

        $sequenceBlock5 = new CurriculumInventorySequenceBlock();
        $sequenceBlock5->setId(5);
        $sequenceBlock5->setDuration(0);
        $sequenceBlock5->setTitle('No Duration');
        $sequenceBlock5->setStartingAcademicLevel($level2);
        $sequenceBlock5->setEndingAcademicLevel($level3);
        $course5 = new Course();
        $course5->setClerkshipType(new CourseClerkshipType());
        $sequenceBlock5->setCourse($course5);

        $sequenceBlock6 = new CurriculumInventorySequenceBlock();
        $sequenceBlock6->setId(6);
        $sequenceBlock6->setDuration(10);
        $sequenceBlock6->setTitle('No Course');
        $sequenceBlock6->setStartingAcademicLevel($level2);
        $sequenceBlock6->setEndingAcademicLevel($level3);

        $report = new CurriculumInventoryReport();
        $report->setSequenceBlocks(
            new ArrayCollection([
                $sequenceBlock1,
                $sequenceBlock2,
                $sequenceBlock3,
                $sequenceBlock4,
                $sequenceBlock5,
                $sequenceBlock6,
            ])
        );

        $data['report'] = $report;
        $data['events'] = [
            1 => ['event_id' => 1, 'duration' => 600, 'method_id' => 'IM008'],
            2 => ['event_id' => 2, 'duration' => 60, 'method_id' => 'AM013'],
            3 => ['event_id' => 3, 'duration' => 120,'method_id' => 'IM004'],
            4 => ['event_id' => 4, 'duration' => 600, 'method_id' => 'IM001'],
            5 => ['event_id' => 5, 'duration' => 90, 'method_id' => 'IM008'],
            6 => ['event_id' => 6, 'duration' => 120, 'method_id' => 'IM026'],
            7 => ['event_id' => 7, 'duration' => 300, 'method_id' => 'IM019'],
            8 => ['event_id' => 8, 'duration' => 90, 'method_id' => 'AM012'],
        ];
        $data['sequence_block_references']['events'] = [
            1 => [
                ['id' => 1, 'event_id' => 1],
                ['id' => 1, 'event_id' => 2],
            ],
            2 => [
                ['id' => 2, 'event_id' => 3],
                ['id' => 2, 'event_id' => 4],
                ['id' => 2, 'event_id' => 5],
            ],
            3 => [
                ['id' => 3, 'event_id' => 6],
                ['id' => 3, 'event_id' => 7],
                ['id' => 3, 'event_id' => 8],
            ],
        ];

        $rows = $this->builder->getClerkshipSequenceBlockInstructionalTime($data);
        $this->assertCount(3, $rows);
        $this->assertEquals([
            'title' => 'Zeppelin Clerkship Year 1',
            'starting_level' => 1,
            'ending_level' => 2,
            'weeks' => 1.0,
            'avg' => 13.5,
        ], $rows[0]);
        $this->assertEquals([
            'title' => 'Aardvark Clerkship Year 2',
            'starting_level' => 2,
            'ending_level' => 3,
            'weeks' => 1.4,
            'avg' => 5,
        ], $rows[1]);
        $this->assertEquals([
            'title' => 'Zeppelin Clerkship Year 2',
            'starting_level' => 2,
            'ending_level' => 3,
            'weeks' => 6.0,
            'avg' => 1.67,
        ], $rows[2]);
    }

    public function testGetInstructionalMethodCounts(): void
    {
        $data['events'] = [
            ['method_id' => 'IM002'],
            ['method_id' => 'IM002'],
            ['method_id' => 'IM001'],
            ['method_id' => 'IM007'],
            ['method_id' => 'AM002'],
            ['method_id' => 'IM008'],
        ];
        $rows = $this->builder->getInstructionalMethodCounts($data);
        $this->assertCount(4, $rows);
        $this->assertEquals([
            'id' => 'IM001',
            'title' => 'Case-Based Instruction/Learning',
            'num_events_primary_method' => 1,
            'num_events_non_primary_method' => 0,
        ], $rows[0]);
        $this->assertEquals([
            'id' => 'IM002',
            'title' => 'Clinical Experience - Ambulatory',
            'num_events_primary_method' => 2,
            'num_events_non_primary_method' => 0,
        ], $rows[1]);
        $this->assertEquals([
            'id' => 'IM007',
            'title' => 'Discussion, Large Group (>12)',
            'num_events_primary_method' => 1,
            'num_events_non_primary_method' => 0,
        ], $rows[2]);
        $this->assertEquals([
            'id' => 'IM008',
            'title' => 'Discussion, Small Group (?12)',
            'num_events_primary_method' => 1,
            'num_events_non_primary_method' => 0,
        ], $rows[3]);
    }

    public function testGetNonClerkshipSequenceBlockAssessmentMethods(): void
    {
        $data = [];

        $level1 = new CurriculumInventoryAcademicLevel();
        $level1->setLevel(1);
        $level2 = new CurriculumInventoryAcademicLevel();
        $level2->setLevel(2);
        $level3 = new CurriculumInventoryAcademicLevel();
        $level3->setLevel(3);

        $sequenceBlock1 = new CurriculumInventorySequenceBlock();
        $sequenceBlock1->setId(1);
        $sequenceBlock1->setTitle('Zeppelin Non-Clerkship Year 2');
        $sequenceBlock1->setStartingAcademicLevel($level2);
        $sequenceBlock1->setEndingAcademicLevel($level3);

        $course1 = new Course();
        $sequenceBlock1->setCourse($course1);

        $sequenceBlock2 = new CurriculumInventorySequenceBlock();
        $sequenceBlock2->setId(2);
        $sequenceBlock2->setTitle('Zeppelin Non-Clerkship Year 1');
        $sequenceBlock2->setStartingAcademicLevel($level1);
        $sequenceBlock2->setEndingAcademicLevel($level2);
        $course2 = new Course();
        $sequenceBlock2->setCourse($course2);

        $sequenceBlock3 = new CurriculumInventorySequenceBlock();
        $sequenceBlock3->setId(3);
        $sequenceBlock3->setTitle('Aardvark Non-Clerkship Year 2');
        $sequenceBlock3->setStartingAcademicLevel($level2);
        $sequenceBlock3->setEndingAcademicLevel($level3);

        $course3 = new Course();
        $sequenceBlock3->setCourse($course3);

        $sequenceBlock4 = new CurriculumInventorySequenceBlock();
        $sequenceBlock4->setId(4);
        $sequenceBlock4->setTitle('Clerkship');
        $course4 = new Course();
        $course4->setClerkshipType(new CourseClerkshipType());
        $sequenceBlock4->setCourse($course4);

        $report = new CurriculumInventoryReport();
        $report->setSequenceBlocks(
            new ArrayCollection([
                $sequenceBlock1, $sequenceBlock2, $sequenceBlock3, $sequenceBlock4,
            ])
        );

        $data['report'] = $report;
        $data['events'] = [
            1 => ['event_id' => 1, 'method_id' => 'AM010', 'assessment_option_name' => 'formative'],
            2 => ['event_id' => 2, 'method_id' => 'AM013', 'assessment_option_name' => 'formative'],
            3 => ['event_id' => 3, 'method_id' => 'AM004', 'assessment_option_name' => 'summative'],
            4 => ['event_id' => 4, 'method_id' => 'AM004', 'assessment_option_name' => 'formative'],
            5 => ['event_id' => 5, 'method_id' => 'AM008', 'assessment_option_name' => 'summative'],
            6 => ['event_id' => 6, 'method_id' => 'AM003', 'assessment_option_name' => 'summative'],
            7 => ['event_id' => 7, 'method_id' => 'AM019', 'assessment_option_name' => 'formative'],
            8 => ['event_id' => 8, 'method_id' => 'AM016', 'assessment_option_name' => 'summative'],
        ];
        $data['sequence_block_references']['events'] = [
            1 => [
                ['id' => 1, 'event_id' => 1],
                ['id' => 1, 'event_id' => 2],
            ],
            2 => [
                ['id' => 2, 'event_id' => 3],
                ['id' => 2, 'event_id' => 4],
                ['id' => 2, 'event_id' => 5],
            ],
            3 => [
                ['id' => 3, 'event_id' => 6],
                ['id' => 3, 'event_id' => 7],
                ['id' => 3, 'event_id' => 8],
            ],
        ];

        $rhett = $this->builder->getNonClerkshipSequenceBlockAssessmentMethods($data);

        $methods = $rhett['methods'];
        $rows = $rhett['rows'];
        $this->assertCount(7, $methods);
        $this->assertEquals([
            'Faculty / resident rating',
            'Internal exams',
            'Lab or practical exams',
            'NBME subject exams',
            'OSCE / SP exam',
            'Other',
            'Paper or oral pres.',
        ], $methods);

        $this->assertEquals([
            'title' => 'Zeppelin Non-Clerkship Year 1',
            'starting_level' => 1,
            'ending_level' => 2,
            'methods' => [
                'Faculty / resident rating' => false,
                'Internal exams' => true,
                'Lab or practical exams' => false,
                'NBME subject exams' => true,
                'OSCE / SP exam' => false,
                'Other' => false,
                'Paper or oral pres.' => false,
            ],
            'num_exams' => 2,
            'has_formative_assessments' => true,
            'has_narrative_assessments' => false,
        ], $rows[0]);

        $this->assertEquals([
            'title' => 'Aardvark Non-Clerkship Year 2',
            'starting_level' => 2,
            'ending_level' => 3,
            'methods' => [
                'Faculty / resident rating' => false,
                'Internal exams' => false,
                'Lab or practical exams' => true,
                'NBME subject exams' => false,
                'OSCE / SP exam' => true,
                'Other' => false,
                'Paper or oral pres.' => true,
            ],
            'num_exams' => 2,
            'has_formative_assessments' => true,
            'has_narrative_assessments' => false,
        ], $rows[1]);

        $this->assertEquals([
            'title' => 'Zeppelin Non-Clerkship Year 2',
            'starting_level' => 2,
            'ending_level' => 3,
            'methods' => [
                'Faculty / resident rating' => true,
                'Internal exams' => false,
                'Lab or practical exams' => false,
                'NBME subject exams' => false,
                'OSCE / SP exam' => false,
                'Other' => true,
                'Paper or oral pres.' => false,
            ],
            'num_exams' => 0,
            'has_formative_assessments' => true,
            'has_narrative_assessments' => true,
        ], $rows[2]);
    }

    public function testGetNonClerkshipSequenceBlockInstructionalTime(): void
    {
        $data = [];

        $level1 = new CurriculumInventoryAcademicLevel();
        $level1->setLevel(1);
        $level2 = new CurriculumInventoryAcademicLevel();
        $level2->setLevel(2);
        $level3 = new CurriculumInventoryAcademicLevel();
        $level3->setLevel(3);

        $sequenceBlock1 = new CurriculumInventorySequenceBlock();
        $sequenceBlock1->setId(1);
        $sequenceBlock1->setDuration(30);
        $sequenceBlock1->setTitle('Zeppelin Non-Clerkship Year 2');
        $sequenceBlock1->setStartingAcademicLevel($level2);
        $sequenceBlock1->setEndingAcademicLevel($level3);
        $course1 = new Course();
        $sequenceBlock1->setCourse($course1);

        $sequenceBlock2 = new CurriculumInventorySequenceBlock();
        $sequenceBlock2->setId(2);
        $sequenceBlock2->setDuration(5);
        $sequenceBlock2->setTitle('Zeppelin Non-Clerkship Year 1');
        $sequenceBlock2->setStartingAcademicLevel($level1);
        $sequenceBlock2->setEndingAcademicLevel($level2);
        $course2 = new Course();
        $sequenceBlock2->setCourse($course2);

        $sequenceBlock3 = new CurriculumInventorySequenceBlock();
        $sequenceBlock3->setId(3);
        $sequenceBlock3->setDuration(7);
        $sequenceBlock3->setTitle('Aardvark Non-Clerkship Year 2');
        $sequenceBlock3->setStartingAcademicLevel($level2);
        $sequenceBlock3->setEndingAcademicLevel($level3);
        $course3 = new Course();
        $sequenceBlock3->setCourse($course3);

        $sequenceBlock4 = new CurriculumInventorySequenceBlock();
        $sequenceBlock4->setId(4);
        $sequenceBlock4->setDuration(5);
        $sequenceBlock4->setTitle('Clerkship');
        $course4 = new Course();
        $course4->setClerkshipType(new CourseClerkshipType());
        $sequenceBlock4->setCourse($course4);

        $sequenceBlock5 = new CurriculumInventorySequenceBlock();
        $sequenceBlock5->setId(5);
        $sequenceBlock5->setDuration(0);
        $sequenceBlock5->setTitle('No Duration');
        $sequenceBlock5->setStartingAcademicLevel($level2);
        $sequenceBlock5->setEndingAcademicLevel($level3);
        $course5 = new Course();
        $sequenceBlock5->setCourse($course5);

        $sequenceBlock6 = new CurriculumInventorySequenceBlock();
        $sequenceBlock6->setId(6);
        $sequenceBlock6->setDuration(10);
        $sequenceBlock6->setTitle('No Course');
        $sequenceBlock6->setStartingAcademicLevel($level2);
        $sequenceBlock6->setEndingAcademicLevel($level3);

        $report = new CurriculumInventoryReport();
        $report->setSequenceBlocks(
            new ArrayCollection([
                $sequenceBlock1,
                $sequenceBlock2,
                $sequenceBlock3,
                $sequenceBlock4,
                $sequenceBlock5,
                $sequenceBlock6,
            ])
        );

        $data['report'] = $report;
        $data['events'] = [
            1 => ['event_id' => 1, 'duration' => 600, 'method_id' => 'IM008'],
            2 => ['event_id' => 2, 'duration' => 60, 'method_id' => 'AM013'],
            3 => ['event_id' => 3, 'duration' => 120,'method_id' => 'IM004'],
            4 => ['event_id' => 4, 'duration' => 600, 'method_id' => 'IM001'],
            5 => ['event_id' => 5, 'duration' => 90, 'method_id' => 'IM008'],
            6 => ['event_id' => 6, 'duration' => 120, 'method_id' => 'IM026'],
            7 => ['event_id' => 7, 'duration' => 300, 'method_id' => 'IM019'],
            8 => ['event_id' => 8, 'duration' => 90, 'method_id' => 'AM012'],
        ];
        $data['sequence_block_references']['events'] = [
            1 => [
                ['id' => 1, 'event_id' => 1],
                ['id' => 1, 'event_id' => 2],
            ],
            2 => [
                ['id' => 2, 'event_id' => 3],
                ['id' => 2, 'event_id' => 4],
                ['id' => 2, 'event_id' => 5],
            ],
            3 => [
                ['id' => 3, 'event_id' => 6],
                ['id' => 3, 'event_id' => 7],
                ['id' => 3, 'event_id' => 8],
            ],
        ];

        $rows = $this->builder->getNonClerkshipSequenceBlockInstructionalTime($data);
        $this->assertCount(3, $rows);
        $this->assertEquals([
            'title' => 'Zeppelin Non-Clerkship Year 1',
            'starting_level' => 1,
            'ending_level' => 2,
            'weeks' => 1.0,
            'avg' => 13.5,
        ], $rows[0]);
        $this->assertEquals([
            'title' => 'Aardvark Non-Clerkship Year 2',
            'starting_level' => 2,
            'ending_level' => 3,
            'weeks' => 1.4,
            'avg' => 5,
        ], $rows[1]);
        $this->assertEquals([
            'title' => 'Zeppelin Non-Clerkship Year 2',
            'starting_level' => 2,
            'ending_level' => 3,
            'weeks' => 6.0,
            'avg' => 1.67,
        ], $rows[2]);
    }

    public function testGetPrimaryInstructionalMethodsByNonClerkshipSequenceBlock(): void
    {
        $level1 = new CurriculumInventoryAcademicLevel();
        $level1->setLevel(1);
        $level2 = new CurriculumInventoryAcademicLevel();
        $level2->setLevel(2);
        $level3 = new CurriculumInventoryAcademicLevel();
        $level3->setLevel(3);

        $report = new CurriculumInventoryReport();
        $sequenceBlock1 = new CurriculumInventorySequenceBlock();
        $sequenceBlock1->setId(1);
        $sequenceBlock1->setTitle('Zeppelin Non-Clerkship Level 2');
        $sequenceBlock1->setStartingAcademicLevel($level2);
        $sequenceBlock1->setEndingAcademicLevel($level3);
        $course1 = new Course();
        $sequenceBlock1->setCourse($course1);

        $sequenceBlock2 = new CurriculumInventorySequenceBlock();
        $sequenceBlock2->setId(2);
        $sequenceBlock2->setTitle('Aardvark Non-Clerkship Level 2');
        $sequenceBlock2->setStartingAcademicLevel($level2);
        $sequenceBlock2->setEndingAcademicLevel($level3);
        $course2 = new Course();
        $sequenceBlock2->setCourse($course2);

        $sequenceBlock3 = new CurriculumInventorySequenceBlock();
        $sequenceBlock3->setId(3);
        $sequenceBlock3->setTitle('Aardvark Non-Clerkship Level 1');
        $sequenceBlock3->setStartingAcademicLevel($level1);
        $sequenceBlock3->setEndingAcademicLevel($level2);
        $course3 = new Course();
        $sequenceBlock3->setCourse($course3);

        $sequenceBlock4 = new CurriculumInventorySequenceBlock();
        $sequenceBlock4->setId(4);
        $sequenceBlock4->setTitle('No events Non-clerkship');
        $sequenceBlock4->setStartingAcademicLevel($level1);
        $sequenceBlock4->setEndingAcademicLevel($level2);
        $course4 = new Course();
        $sequenceBlock4->setCourse($course4);

        $sequenceBlock5 = new CurriculumInventorySequenceBlock();
        $sequenceBlock5->setId(5);
        $sequenceBlock5->setTitle('Clerkship');
        $sequenceBlock5->setStartingAcademicLevel($level1);
        $sequenceBlock5->setEndingAcademicLevel($level2);
        $course5 = new Course();
        $course5->setClerkshipType(new CourseClerkshipType());
        $sequenceBlock5->setCourse($course5);

        $report->setSequenceBlocks(
            new ArrayCollection([
                $sequenceBlock1,
                $sequenceBlock2,
                $sequenceBlock3,
                $sequenceBlock4,
                $sequenceBlock5,
            ])
        );

        $data = [
            'report' => $report,
            'events' => [
                1 => ['method_id' => 'IM001', 'duration' => 60],
                2 => ['method_id' => 'IM002', 'duration' => 120],
                3 => ['method_id' => 'IM001', 'duration' => 90],
                4 => ['method_id' => 'IM004', 'duration' => 240],
                5 => ['method_id' => 'IM010', 'duration' => 30],
                6 => ['method_id' => 'AM001'],
            ],
            'sequence_block_references' => [
                'events' => [
                    1 => [
                        ['event_id' => 1],
                        ['event_id' => 2],
                    ],
                    2 => [
                        ['event_id' => 3],
                        ['event_id' => 4],
                    ],
                    3 => [
                        ['event_id' => 5],
                        ['event_id' => 6],
                    ],
                ],
            ],
        ];
        $rhett = $this->builder->getPrimaryInstructionalMethodsByNonClerkshipSequenceBlock($data);
        $methods = $rhett['methods'];
        $this->assertCount(2, $methods);
        $this->assertEquals(['title' => 'Other', 'total' => 420], $methods[0]);
        $this->assertEquals(['title' => 'Patient Contact', 'total' => 120], $methods[1]);
        $rows = $rhett['rows'];
        $this->assertCount(3, $rows);
        $this->assertEquals([
            'title' => 'Aardvark Non-Clerkship Level 1',
            'starting_level' => 1,
            'ending_level' => 2,
            'instructional_methods' => [
                'Other' => 30,
            ],
            'total' => 30,
        ], $rows[0]);
        $this->assertEquals([
            'title' => 'Aardvark Non-Clerkship Level 2',
            'starting_level' => 2,
            'ending_level' => 3,
            'instructional_methods' => [
                'Other' => 330,
            ],
            'total' => 330,
        ], $rows[1]);
        $this->assertEquals([
            'title' => 'Zeppelin Non-Clerkship Level 2',
            'starting_level' => 2,
            'ending_level' => 3,
            'instructional_methods' => [
                'Other' => 60,
                'Patient Contact' => 120,
            ],
            'total' => 180,
        ], $rows[2]);
    }

    public function testGetProgramExpectationsMappedToPcrs(): void
    {
        $data['expectations'] = [
            'program_objectives' => [
                ['id' => 1, 'title' => 'Objective 1'],
                ['id' => 2, 'title' => '<p>Objective 2</p>'],
                ['id' => 3, 'title' => 'Objective 3'],
                ['id' => 4, 'title' => 'Objective 4'],
            ],
            'framework' => [
                'relations' => [
                    'program_objectives_to_pcrs' => [
                        ['rel1' => 1, 'rel2' => 'aamc-pcrs-comp-c0101'],
                        ['rel1' => 1, 'rel2' => 'aamc-pcrs-comp-c0102'],
                        ['rel1' => 2, 'rel2' => 'aamc-pcrs-comp-c0103'],
                        ['rel1' => 3, 'rel2' => 'aamc-pcrs-comp-c0104'],
                        ['rel1' => 3, 'rel2' => 'aamc-pcrs-comp-c0105'],
                    ],
                ],
            ],
        ];

        $rows = $this->builder->getProgramExpectationsMappedToPcrs($data);
        $this->assertCount(3, $rows);
        $this->assertEquals('Objective 1', $rows[0]['title']);
        $this->assertCount(2, $rows[0]['pcrs']);
        $this->assertEquals(
            'c0101: Perform all medical, diagnostic, and surgical procedures considered essential '
            . 'for the area of practice',
            $rows[0]['pcrs'][0]
        );
        $this->assertEquals(
            'c0102: Gather essential and accurate information about patients and their conditions through '
            . 'history-taking, physical examination, and the use of laboratory data, imaging and other tests',
            $rows[0]['pcrs'][1]
        );
        $this->assertEquals('<p>Objective 2</p>', $rows[1]['title']);
        $this->assertCount(1, $rows[1]['pcrs']);
        $this->assertEquals(
            'c0103: Organize and prioritize responsibilities to provide care that is safe, effective, and efficient',
            $rows[1]['pcrs'][0]
        );
        $this->assertEquals('Objective 3', $rows[2]['title']);
        $this->assertCount(2, $rows[2]['pcrs']);
        $this->assertEquals(
            'c0104: Interpret laboratory data, imaging studies, and other tests required for the area of practice',
            $rows[2]['pcrs'][0]
        );
        $this->assertEquals(
            'c0105: Make informed decisions about diagnostic and therapeutic interventions based on patient '
             . 'information and preferences, up-to-date scientific evidence, and clinical judgment',
            $rows[2]['pcrs'][1]
        );
    }

    public function testBuild(): void
    {
        $report = new CurriculumInventoryReport();
        $this->aggregator->shouldReceive('getData')->andReturn([
            'report' => $report,
            'events' => [],
            'sequence_block_references' => [
                'events' => [],
            ],
            'expectations' => [
                'program_objectives' => [],
                'framework' => [
                    'relations' => [
                        'program_objectives_to_pcrs' => [],
                    ],
                ],
            ],
        ]);
        $rhett = $this->builder->build($report);
        $this->assertCount(9, $rhett);
        $this->assertArrayHasKey('program_expectations_mapped_to_pcrs', $rhett);
        $this->assertArrayHasKey('primary_instructional_methods_by_non_clerkship_sequence_blocks', $rhett);
        $this->assertArrayHasKey('non_clerkship_sequence_block_instructional_time', $rhett);
        $this->assertArrayHasKey('clerkship_sequence_block_instructional_time', $rhett);
        $this->assertArrayHasKey('instructional_method_counts', $rhett);
        $this->assertArrayHasKey('non_clerkship_sequence_block_assessment_methods', $rhett);
        $this->assertArrayHasKey('clerkship_sequence_block_assessment_methods', $rhett);
        $this->assertArrayHasKey('all_events_with_assessments_tagged_as_formative_or_summative', $rhett);
        $this->assertArrayHasKey('all_resource_types', $rhett);
    }
}
