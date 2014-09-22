<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface AamcPcrsInterface
 */
interface AamcPcrsInterface 
{
    public function setPcrsId($pcrsId);

    public function getPcrsId();

    public function setDescription($description);

    public function getDescription();

    public function addCompetency(\Ilios\CoreBundle\Model\Competency $competencies);

    public function removeCompetency(\Ilios\CoreBundle\Model\Competency $competencies);

    public function getCompetencies();
}
