<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\IdentifiableTraitIntertface;
use Ilios\CoreBundle\Traits\NameableTraitInterface;

use Ilios\CoreBundle\Model\SchoolInterface;

/**
 * Interface CurriculumInventoryInstitutionInterface
 */
interface CurriculumInventoryInstitutionInterface extends IdentifiableTraitIntertface, NameableTraitInterface
{
    /**
     * @param string $aamcCode
     */
    public function setAamcCode($aamcCode);

    /**
     * @return string
     */
    public function getAamcCode();

    /**
     * @param string $addressStreet
     */
    public function setAddressStreet($addressStreet);

    /**
     * @return string
     */
    public function getAddressStreet();

    /**
     * @param string $addressCity
     */
    public function setAddressCity($addressCity);

    /**
     * @return string
     */
    public function getAddressCity();

    /**
     * @param string $addressStateOrProvince
     */
    public function setAddressStateOrProvince($addressStateOrProvince);

    /**
     * @return string
     */
    public function getAddressStateOrProvince();

    /**
     * @param string $addressZipcode
     */
    public function setAddressZipcode($addressZipcode);

    /**
     * @return string
     */
    public function getAddressZipcode();

    /**
     * @param string $addressCountryCode
     */
    public function setAddressCountryCode($addressCountryCode);

    /**
     * @return string
     */
    public function getAddressCountryCode();

    /**
     * @param SchoolInterface $school
     */
    public function setSchool(SchoolInterface $school);

    /**
     * @return SchoolInterface
     */
    public function getSchool();
}

