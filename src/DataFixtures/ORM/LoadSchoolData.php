<?php

declare(strict_types=1);

namespace App\DataFixtures\ORM;

use App\Entity\School;
use App\Entity\SchoolInterface;
use App\Service\DataimportFileLocator;

/**
 * Class LoadSchoolData
 */
class LoadSchoolData extends AbstractFixture
{
    public function __construct(DataimportFileLocator $dataimportFileLocator)
    {
        parent::__construct($dataimportFileLocator, 'school');
    }

    /**
     * @return SchoolInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new School();
    }


    /**
     * @param SchoolInterface $entity
     * @param array $data
     * @return SchoolInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `school_id`,`template_prefix`,`title`,`ilios_administrator_email`,`change_alert_recipients`,
        // `academic_year_start_day`, `academic_year_start_month`, `academic_year_end_day`, `academic_year_end_month`
        $entity->setId($data[0]);
        $entity->setTemplatePrefix($data[1]);
        $entity->setTitle($data[2]);
        $entity->setIliosAdministratorEmail($data[3]);
        $entity->setChangeAlertRecipients($data[4]);
        $entity->setAcademicYearStartDay((int) $data[5]);
        $entity->setAcademicYearStartMonth((int) $data[6]);
        $entity->setAcademicYearEndDay((int) $data[7]);
        $entity->setAcademicYearEndMonth((int) $data[8]);
        return $entity;
    }
}
