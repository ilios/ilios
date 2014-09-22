<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface ReportInterface
 */
interface ReportInterface 
{
    public function getReportId();

    public function setCreationDate($creationDate);

    public function getCreationDate();

    public function setSubject($subject);

    public function getSubject();

    public function setPrepositionalObject($prepositionalObject);

    public function getPrepositionalObject();

    public function setDeleted($deleted);

    public function getDeleted();

    public function setTitle($title);

    public function getTitle();

    public function setUser(\Ilios\CoreBundle\Model\User $user = null);

    public function getUser();
}

