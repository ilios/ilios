<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface IlmSessionFacetInterface
 */
interface IlmSessionFacetInterface 
{
    public function getIlmSessionFacetId();

    public function setHours($hours);

    public function getHours();

    public function setDueDate($dueDate);

    public function getDueDate();

    public function addGroup(\Ilios\CoreBundle\Model\Group $groups);

    public function removeGroup(\Ilios\CoreBundle\Model\Group $groups);

    public function getGroups();

    public function addInstructorGroup(\Ilios\CoreBundle\Model\InstructorGroup $instructorGroups);

    public function removeInstructorGroup(\Ilios\CoreBundle\Model\InstructorGroup $instructorGroups);

    public function getInstructorGroups();

    public function addInstructor(\Ilios\CoreBundle\Model\User $instructors);

    public function removeInstructor(\Ilios\CoreBundle\Model\User $instructors);

    public function getInstructors();

    public function addLearner(\Ilios\CoreBundle\Model\User $learners);

    public function removeLearner(\Ilios\CoreBundle\Model\User $learners);

    public function getLearners();
}
