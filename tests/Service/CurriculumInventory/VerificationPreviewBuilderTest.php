<?php

namespace App\Tests\Service\CurriculumInventory;

use App\Entity\DTO\AamcMethodDTO;
use App\Entity\DTO\AamcPcrsDTO;
use App\Entity\Manager\AamcMethodManager;
use App\Entity\Manager\AamcPcrsManager;
use App\Service\CurriculumInventory\Export\Aggregator;
use App\Service\CurriculumInventory\VerificationPreviewBuilder;
use App\Tests\TestCase;
use Mockery as m;

/**
 * Class VerificationPreviewBuilderTest
 * @package App\Tests\Service\CurriculumInventory
 */
class VerificationPreviewBuilderTest extends TestCase
{

    /**
     * @var VerificationPreviewBuilder
     */
    protected $builder;

    /**
     * @var AamcMethodManager|m\MockInterface
     */
    protected $methodManager;

    /**
     * @var AamcPcrsManager|m\MockInterface
     */
    protected $pcrsManager;

    /**
     * @var Aggregator|m\MockInterface
     */
    protected $aggregator;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->aggregator = m::mock(Aggregator::class);
        $this->methodManager = m::mock(AamcMethodManager::class);
        $this->pcrsManager = m::mock(AamcPcrsManager::class);
        $this->builder = new VerificationPreviewBuilder(
            $this->aggregator,
            $this->methodManager,
            $this->pcrsManager
        );

        $this->methodManager->shouldReceive('findDTOsBy')->andReturn(
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

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        unset($this->aggregator);
        unset($this->methodManager);
        unset($this->pcrsManager);
        unset($this->builder);
        parent::tearDown();
    }

    /**
     * @covers VerificationPreviewBuilder::getAllEventsWithAssessmentsTaggedAsFormativeOrSummative
     */
    public function testGetAllEventsWithAssessmentsTaggedAsFormativeOrSummative()
    {
        // @todo implement [ST 2019/09/09]
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers VerificationPreviewBuilder::getAllResourceTypes
     */
    public function testGetAllResourceTypes()
    {
        // @todo implement [ST 2019/09/09]
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers VerificationPreviewBuilder::getClerkshipSequenceBlockAssessmentMethods
     */
    public function testGetClerkshipSequenceBlockAssessmentMethods()
    {
        // @todo implement [ST 2019/09/09]
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers VerificationPreviewBuilder::getClerkshipSequenceBlockInstructionalTime
     */
    public function testGetClerkshipSequenceBlockInstructionalTime()
    {
        // @todo implement [ST 2019/09/09]
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers VerificationPreviewBuilder::getInstructionalMethodCounts
     */
    public function testGetInstructionalMethodCounts()
    {
        // @todo implement [ST 2019/09/09]
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers VerificationPreviewBuilder::getNonClerkshipSequenceBlockAssessmentMethods
     */
    public function testGetNonClerkshipSequenceBlockAssessmentMethods()
    {
        // @todo implement [ST 2019/09/09]
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers VerificationPreviewBuilder::getNonClerkshipSequenceBlockInstructionalTime
     */
    public function testGetNonClerkshipSequenceBlockInstructionalTime()
    {
        // @todo implement [ST 2019/09/09]
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers VerificationPreviewBuilder::getPrimaryInstructionalMethodsByNonClerkshipSequenceBlock
     */
    public function testGetPrimaryInstructionalMethodsByNonClerkshipSequenceBlock()
    {
        // @todo implement [ST 2019/09/09]
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers VerificationPreviewBuilder::getProgramExpectationsMappedToPCRS
     */
    public function testGetProgramExpectationsMappedToPCRS()
    {
        // @todo implement [ST 2019/09/09]
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers VerificationPreviewBuilder::getSequenceBlockInstructionalTime
     */
    public function testGetSequenceBlockInstructionalTime()
    {
        // @todo implement [ST 2019/09/09]
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @covers VerificationPreviewBuilder::build
     */
    public function testBuild()
    {
        // @todo implement [ST 2019/09/09]
        $this->markTestIncomplete('This test has not been implemented yet.');
    }
}
