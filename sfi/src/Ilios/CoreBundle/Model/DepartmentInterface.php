<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface DepartmentInterface
 */
interface DepartmentInterface 
{
    public function getDepartmentId();

    public function setTitle($title);

    public function getTitle();

    public function setSchool(\Ilios\CoreBundle\Model\School $school = null);

    public function getSchool();

    public function setDeleted($deleted);

    public function getDeleted();
}
