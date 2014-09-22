<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface SessionTypeInterface
 */
interface SessionTypeInterface 
{
    public function getSessionTypeId();

    public function setTitle($title);

    public function getTitle();

    public function setSessionTypeCssClass($sessionTypeCssClass);

    public function getSessionTypeCssClass();

    public function setAssessment($assessment);

    public function getAssessment();

    public function setAssessmentOption(\Ilios\CoreBundle\Model\AssessmentOption $assessmentOption = null);

    public function getAssessmentOption();

    public function setOwningSchool(\Ilios\CoreBundle\Model\School $owningSchool = null);

    public function getOwningSchool();

    public function addAamcMethod(\Ilios\CoreBundle\Model\AamcMethod $aamcMethods);

    public function removeAamcMethod(\Ilios\CoreBundle\Model\AamcMethod $aamcMethods);

    public function getAamcMethods();
}
