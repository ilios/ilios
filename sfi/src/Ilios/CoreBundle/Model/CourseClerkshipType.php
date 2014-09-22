<?php

namespace Ilios\CoreBundle\Model;



/**
 * CourseClerkshipType
 */
class CourseClerkshipType
{
    /**
     * @var integer
     */
    private $courseClerkshipTypeId;

    /**
     * @var string
     */
    private $title;


    /**
     * Get courseClerkshipTypeId
     *
     * @return integer 
     */
    public function getCourseClerkshipTypeId()
    {
        return $this->courseClerkshipTypeId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return CourseClerkshipType
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }
}
