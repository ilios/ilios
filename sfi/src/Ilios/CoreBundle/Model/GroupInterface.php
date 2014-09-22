<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface GroupInterface
 */
interface GroupInterface 
{
    public function getGroupId();

    public function setTitle($title);

    public function getTitle();

    public function setInstructors($instructors);

    public function getInstructors();

    public function setLocation($location);

    public function getLocation();

    public function setCohort(\Ilios\CoreBundle\Model\Cohort $cohort = null);

    public function getCohort();

    public function addUser(\Ilios\CoreBundle\Model\User $users);

    public function removeUser(\Ilios\CoreBundle\Model\User $users);

    public function getUsers();

    public function addInstructorUser(\Ilios\CoreBundle\Model\User $instructorUsers);

    public function removeInstructorUser(\Ilios\CoreBundle\Model\User $instructorUsers);

    public function getInstructorUsers();

    public function addInstructorGroup(\Ilios\CoreBundle\Model\InstructorGroup $instructorGroups);

    public function removeInstructorGroup(\Ilios\CoreBundle\Model\InstructorGroup $instructorGroups);

    public function getInstructorGroups();

    public function addIlmSessionFacet(\Ilios\CoreBundle\Model\IlmSessionFacet $ilmSessionFacets);

    public function removeIlmSessionFacet(\Ilios\CoreBundle\Model\IlmSessionFacet $ilmSessionFacets);

    public function getIlmSessionFacets();

    public function addOffering(\Ilios\CoreBundle\Model\Offering $offerings);

    public function removeOffering(\Ilios\CoreBundle\Model\Offering $offerings);

    public function getOfferings();

    public function addParent(\Ilios\CoreBundle\Model\Group $parent);

    public function removeParent(\Ilios\CoreBundle\Model\Group $parent);

    public function getParents();
}
