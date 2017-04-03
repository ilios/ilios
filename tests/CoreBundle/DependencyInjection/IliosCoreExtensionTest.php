<?php

namespace Tests\CoreBundle\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Ilios\CoreBundle\DependencyInjection\IliosCoreExtension;

class IliosCoreExtensionTest extends AbstractExtensionTestCase
{

    protected function getContainerExtensions()
    {
        return array(
            new IliosCoreExtension()
        );
    }

    public function testParametersSet()
    {
        $fileSystemStoragePath = '/tmp/test';
        $ldapUrl = 'ldap.url';
        $ldapUser = 'ldap.user';
        $ldapPassword = 'ldap.pass';
        $ldapSearchBase = 'ldap.base';
        $ldapCampusIdProperty = 'ldap.camp';
        $ldapUsernameProperty = 'ldap.username';
        $trackingEnabled = true;
        $trackingCode = 'UA-XXXXXXXX-1';
        $this->load(array(
            'file_system_storage_path' => $fileSystemStoragePath,
            'ldap_directory_url' => $ldapUrl,
            'ldap_directory_user' => $ldapUser,
            'ldap_directory_password' => $ldapPassword,
            'ldap_directory_search_base' => $ldapSearchBase,
            'ldap_directory_campus_id_property' => $ldapCampusIdProperty,
            'ldap_directory_username_property' => $ldapUsernameProperty,
            'institution_domain' => 'ucsf.edu',
            'supporting_link' => 'https://inventory.ucsf.edu',
            'timezone' => 'America\Los_Angeles',
            'enable_tracking' => $trackingEnabled,
            'tracking_code' => $trackingCode,
        ));
        $parameters = array(
            'ilioscore.basemanager.class' => 'Ilios\CoreBundle\Entity\Manager\BaseManager',
            'ilioscore.dtomanager.class' => 'Ilios\CoreBundle\Entity\Manager\DTOManager',
            'ilioscore.auditlog.manager.class' => 'Ilios\CoreBundle\Entity\Manager\AuditLogManager',
            'ilioscore.authentication.manager.class' => 'Ilios\CoreBundle\Entity\Manager\AuthenticationManager',
            'ilioscore.courselearningmaterial.manager.class' =>
                'Ilios\CoreBundle\Entity\Manager\CourseLearningMaterialManager',
            'ilioscore.course.manager.class' => 'Ilios\CoreBundle\Entity\Manager\CourseManager',
            'ilioscore.curriculuminventoryreport.manager.class' =>
                'Ilios\CoreBundle\Entity\Manager\CurriculumInventoryReportManager',
            'ilioscore.learningmaterial.manager.class' => 'Ilios\CoreBundle\Entity\Manager\LearningMaterialManager',
            'ilioscore.meshdescriptor.manager.class' => 'Ilios\CoreBundle\Entity\Manager\MeshDescriptorManager',
            'ilioscore.objective.manager.class' => 'Ilios\CoreBundle\Entity\Manager\ObjectiveManager',
            'ilioscore.offering.manager.class' => 'Ilios\CoreBundle\Entity\Manager\OfferingManager',
            'ilioscore.pendinguserupdate.manager.class' => 'Ilios\CoreBundle\Entity\Manager\PendingUserUpdateManager',
            'ilioscore.programyearsteward.manager.class' => 'Ilios\CoreBundle\Entity\Manager\ProgramYearStewardManager',
            'ilioscore.school.manager.class' => 'Ilios\CoreBundle\Entity\Manager\SchoolManager',
            'ilioscore.sessiondescription.manager.class' => 'Ilios\CoreBundle\Entity\Manager\SessionDescriptionManager',
            'ilioscore.sessionlearningmaterial.manager.class' =>
                'Ilios\CoreBundle\Entity\Manager\SessionLearningMaterialManager',
            'ilioscore.user.manager.class' => 'Ilios\CoreBundle\Entity\Manager\UserManager',
            'ilioscore.dataloader.aamcmethod.class' => 'Tests\CoreBundle\DataLoader\AamcMethodData',
            'ilioscore.dataloader.aamcpcrs.class' => 'Tests\CoreBundle\DataLoader\AamcPcrsData',
            'ilioscore.dataloader.alertchangetype.class' => 'Tests\CoreBundle\DataLoader\AlertChangeTypeData',
            'ilioscore.dataloader.alert.class' => 'Tests\CoreBundle\DataLoader\AlertData',
            'ilioscore.dataloader.assessmentoption.class' => 'Tests\CoreBundle\DataLoader\AssessmentOptionData',
            'ilioscore.dataloader.cohort.class' => 'Tests\CoreBundle\DataLoader\CohortData',
            'ilioscore.dataloader.competency.class' => 'Tests\CoreBundle\DataLoader\CompetencyData',
            'ilioscore.dataloader.courselearningmaterial.class' =>
                'Tests\CoreBundle\DataLoader\CourseLearningMaterialData',
            'ilioscore.dataloader.course.class' => 'Tests\CoreBundle\DataLoader\CourseData',
            'ilioscore.dataloader.courseclerkshiptype.class' =>
                'Tests\CoreBundle\DataLoader\CourseClerkshipTypeData',
            'ilioscore.dataloader.curriculuminventoryacademiclevel.class' =>
                'Tests\CoreBundle\DataLoader\CurriculumInventoryAcademicLevelData',
            'ilioscore.dataloader.curriculuminventoryexport.class' =>
                'Tests\CoreBundle\DataLoader\CurriculumInventoryExportData',
            'ilioscore.dataloader.curriculuminventoryinstitution.class' =>
                'Tests\CoreBundle\DataLoader\CurriculumInventoryInstitutionData',
            'ilioscore.dataloader.curriculuminventoryreport.class' =>
                'Tests\CoreBundle\DataLoader\CurriculumInventoryReportData',
            'ilioscore.dataloader.curriculuminventorysequenceblock.class' =>
                'Tests\CoreBundle\DataLoader\CurriculumInventorySequenceBlockData',
            'ilioscore.dataloader.curriculuminventorysequence.class' =>
                'Tests\CoreBundle\DataLoader\CurriculumInventorySequenceData',
            'ilioscore.dataloader.department.class' => 'Tests\CoreBundle\DataLoader\DepartmentData',
            'ilioscore.dataloader.ilmsession.class' => 'Tests\CoreBundle\DataLoader\IlmSessionData',
            'ilioscore.dataloader.ingestionexception.class' =>
                'Tests\CoreBundle\DataLoader\IngestionExceptionData',
            'ilioscore.dataloader.instructorgroup.class' => 'Tests\CoreBundle\DataLoader\InstructorGroupData',
            'ilioscore.dataloader.learnergroup.class' => 'Tests\CoreBundle\DataLoader\LearnerGroupData',
            'ilioscore.dataloader.learningmaterialstatus.class' =>
                'Tests\CoreBundle\DataLoader\LearningMaterialStatusData',
            'ilioscore.dataloader.learningmaterialuserrole.class' =>
                'Tests\CoreBundle\DataLoader\LearningMaterialUserRoleData',
            'ilioscore.dataloader.learningmaterial.class' =>
                'Tests\CoreBundle\DataLoader\LearningMaterialData',
            'ilioscore.dataloader.objective.class' => 'Tests\CoreBundle\DataLoader\ObjectiveData',
            'ilioscore.dataloader.offering.class' => 'Tests\CoreBundle\DataLoader\OfferingData',
            'ilioscore.dataloader.programyear.class' => 'Tests\CoreBundle\DataLoader\ProgramYearData',
            'ilioscore.dataloader.programyearsteward.class' =>
                'Tests\CoreBundle\DataLoader\ProgramYearStewardData',
            'ilioscore.dataloader.program.class' => 'Tests\CoreBundle\DataLoader\ProgramData',
            'ilioscore.dataloader.report.class' => 'Tests\CoreBundle\DataLoader\ReportData',
            'ilioscore.dataloader.school.class' => 'Tests\CoreBundle\DataLoader\SchoolData',
            'ilioscore.dataloader.sessiondescription.class' =>
                'Tests\CoreBundle\DataLoader\SessionDescriptionData',
            'ilioscore.dataloader.sessionlearningmaterial.class' =>
                'Tests\CoreBundle\DataLoader\SessionLearningMaterialData',
            'ilioscore.dataloader.sessiontype.class' => 'Tests\CoreBundle\DataLoader\SessionTypeData',
            'ilioscore.dataloader.session.class' =>
                'Tests\CoreBundle\DataLoader\SessionData',
            'ilioscore.dataloader.term.class' => 'Tests\CoreBundle\DataLoader\TermData',
            'ilioscore.dataloader.usermadereminder.class' => 'Tests\CoreBundle\DataLoader\UserMadeReminderData',
            'ilioscore.dataloader.userrole.class' => 'Tests\CoreBundle\DataLoader\UserRoleData',
            'ilioscore.dataloader.user.class' => 'Tests\CoreBundle\DataLoader\UserData',
            'ilioscore.dataloader.vocabulary.class' => 'Tests\CoreBundle\DataLoader\VocabularyData',
            'ilios_core.ldap.url' => $ldapUrl,
            'ilios_core.ldap.user' => $ldapUser,
            'ilios_core.ldap.password' => $ldapPassword,
            'ilios_core.ldap.search_base' => $ldapSearchBase,
            'ilios_core.ldap.campus_id_property' => $ldapCampusIdProperty,
            'ilios_core.ldap.username_property' => $ldapUsernameProperty,
            'ilios_core.file_store_path' => $fileSystemStoragePath,
            'ilios_core.institution_domain' => 'ucsf.edu',
            'ilios_core.supporting_link' => 'https://inventory.ucsf.edu',
            'ilios_core.enable_tracking' => $trackingEnabled,
            'ilios_core.tracking_code' => $trackingCode,
        );
        foreach ($parameters as $name => $value) {
            $this->assertContainerBuilderHasParameter($name, $value);
        }
        $services = array(
            'ilioscore.aamcmethod.manager',
            'ilioscore.aamcpcrs.manager',
            'ilioscore.alert.manager',
            'ilioscore.alertchangetype.manager',
            'ilioscore.assessmentoption.manager',
            'ilioscore.cohort.manager',
            'ilioscore.competency.manager',
            'ilioscore.course.manager',
            'ilioscore.courseclerkshiptype.manager',
            'ilioscore.courselearningmaterial.manager',
            'ilioscore.curriculuminventoryacademiclevel.manager',
            'ilioscore.curriculuminventoryexport.manager',
            'ilioscore.curriculuminventoryinstitution.manager',
            'ilioscore.curriculuminventoryreport.manager',
            'ilioscore.curriculuminventorysequence.manager',
            'ilioscore.curriculuminventorysequenceblock.manager',
            'ilioscore.department.manager',
            'ilioscore.ilmsession.manager',
            'ilioscore.ingestionexception.manager',
            'ilioscore.instructorgroup.manager',
            'ilioscore.learnergroup.manager',
            'ilioscore.learningmaterialstatus.manager',
            'ilioscore.learningmaterialuserrole.manager',
            'ilioscore.meshconcept.manager',
            'ilioscore.meshdescriptor.manager',
            'ilioscore.meshpreviousindexing.manager',
            'ilioscore.meshqualifier.manager',
            'ilioscore.meshsemantictype.manager',
            'ilioscore.meshterm.manager',
            'ilioscore.objective.manager',
            'ilioscore.offering.manager',
            'ilioscore.permission.manager',
            'ilioscore.program.manager',
            'ilioscore.programyear.manager',
            'ilioscore.programyearsteward.manager',
            'ilioscore.report.manager',
            'ilioscore.school.manager',
            'ilioscore.session.manager',
            'ilioscore.sessiondescription.manager',
            'ilioscore.sessionlearningmaterial.manager',
            'ilioscore.sessiontype.manager',
            'ilioscore.term.manager',
            'ilioscore.user.manager',
            'ilioscore.usermadereminder.manager',
            'ilioscore.userrole.manager',
            'ilioscore.vocabulary.manager',
            'ilioscore.dataloader.aamcmethod',
            'ilioscore.dataloader.aamcpcrs',
            'ilioscore.dataloader.alertchangetype',
            'ilioscore.dataloader.alert',
            'ilioscore.dataloader.assessmentoption',
            'ilioscore.dataloader.cohort',
            'ilioscore.dataloader.competency',
            'ilioscore.dataloader.courselearningmaterial',
            'ilioscore.dataloader.course',
            'ilioscore.dataloader.courseclerkshiptype',
            'ilioscore.dataloader.curriculuminventoryacademiclevel',
            'ilioscore.dataloader.curriculuminventoryexport',
            'ilioscore.dataloader.curriculuminventoryinstitution',
            'ilioscore.dataloader.curriculuminventoryreport',
            'ilioscore.dataloader.curriculuminventorysequenceblock',
            'ilioscore.dataloader.curriculuminventorysequence',
            'ilioscore.dataloader.department',
            'ilioscore.dataloader.ilmsession',
            'ilioscore.dataloader.ingestionexception',
            'ilioscore.dataloader.instructorgroup',
            'ilioscore.dataloader.learnergroup',
            'ilioscore.dataloader.learningmaterialstatus',
            'ilioscore.dataloader.learningmaterialuserrole',
            'ilioscore.dataloader.learningmaterial',
            'ilioscore.dataloader.objective',
            'ilioscore.dataloader.offering',
            'ilioscore.dataloader.programyear',
            'ilioscore.dataloader.programyearsteward',
            'ilioscore.dataloader.program',
            'ilioscore.dataloader.report',
            'ilioscore.dataloader.school',
            'ilioscore.dataloader.sessiondescription',
            'ilioscore.dataloader.sessionlearningmaterial',
            'ilioscore.dataloader.sessiontype',
            'ilioscore.dataloader.session',
            'ilioscore.dataloader.term',
            'ilioscore.dataloader.usermadereminder',
            'ilioscore.dataloader.userrole',
            'ilioscore.dataloader.user',
            'ilioscore.dataloader.vocabulary',
            'ilioscore.temporary_filesystem',
            'ilioscore.filesystem',
            'ilioscore.logger',
            'ilioscore.ldapmanager',
            'ilioscore.directory',
        );
        foreach ($services as $service) {
            $this->assertContainerBuilderHasService($service);
        }
    }
}
