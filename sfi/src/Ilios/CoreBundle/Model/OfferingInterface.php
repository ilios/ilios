<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface OfferingInterface
 */
interface OfferingInterface 
{
    public function getOfferingId();

    public function setRoom($room);

    public function getRoom();

    public function setStartDate($startDate);

    public function getStartDate();

    public function setEndDate($endDate);

    public function getEndDate();

    public function setDeleted($deleted);

    public function getDeleted();

    public function setLastUpdatedOn($lastUpdatedOn);

    public function getLastUpdatedOn();

    public function setSession(\Ilios\CoreBundle\Model\Session $session = null);

    public function getSession();

    public function addGroup(\Ilios\CoreBundle\Model\Group $groups);

    public function removeGroup(\Ilios\CoreBundle\Model\Group $groups);

    public function getGroups();

    public function addInstructorGroup(\Ilios\CoreBundle\Model\InstructorGroup $instructorGroups);

    public function removeInstructorGroup(\Ilios\CoreBundle\Model\InstructorGroup $instructorGroups);

    public function getInstructorGroups();

    public function addUser(\Ilios\CoreBundle\Model\User $users);

    public function removeUser(\Ilios\CoreBundle\Model\User $users);

    public function getUsers();

    public function addReccuringEvent(\Ilios\CoreBundle\Model\RecurringEvent $reccuringEvents);

    public function removeReccuringEvent(\Ilios\CoreBundle\Model\RecurringEvent $reccuringEvents);

    public function getReccuringEvents();

    public function setPublishEvent(\Ilios\CoreBundle\Model\PublishEvent $publishEvent = null);

    public function getPublishEvent();
}
