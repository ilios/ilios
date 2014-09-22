<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface InstructorGroupInterface
 */
interface InstructorGroupInterface 
{
    public function getInstructorGroupId();

    public function setTitle($title);

    public function getTitle();

    public function setSchoolId($schoolId);

    public function getSchoolId();

    public function addGroup(\Ilios\CoreBundle\Model\Group $groups);

    public function removeGroup(\Ilios\CoreBundle\Model\Group $groups);

    public function getGroups();

    public function addIlmSessionFacet(\Ilios\CoreBundle\Model\IlmSessionFacet $ilmSessionFacets);

    public function removeIlmSessionFacet(\Ilios\CoreBundle\Model\IlmSessionFacet $ilmSessionFacets);

    public function getIlmSessionFacets();

    public function addUser(\Ilios\CoreBundle\Model\User $users);

    public function removeUser(\Ilios\CoreBundle\Model\User $users);

    public function getUsers();

    public function addOffering(\Ilios\CoreBundle\Model\Offering $offerings);

    public function removeOffering(\Ilios\CoreBundle\Model\Offering $offerings);

    public function getOfferings();
}

