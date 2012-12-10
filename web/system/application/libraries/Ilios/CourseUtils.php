<?php

/**
 * Static class providing course-related utilities.
 */
class Ilios2_CourseUtils
{
    /**
     * Course id hash postfix.
     * @var string
     */
    const COURSE_ID_HASH_POSTFIX = '00000';
    /**
     * Course id hash prefix.
     * @var string
     */
    const COURSE_ID_HASH_PREFIX = 'ILIOS';


    /**
     * Generates a hash ("unique id") from a given course id.
     * @param int $courseId the course id
     * @return string the generated hash
     */
    public static function generateHashFromCourseId ($courseId)
    {
        $val = self::COURSE_ID_HASH_PREFIX . strtoupper(base_convert($courseId . self::COURSE_ID_HASH_POSTFIX, 10, 36));
        return $val;
    }

    /**
     * Extracts a course id from a given hash ("unique id").
     * @param string $hash
     * @return int the extracted course id
     */
    public static function extractCourseIdFromHash ($hash)
    {
    	$val = (int) substr(base_convert(substr($hash, strlen(self::COURSE_ID_HASH_PREFIX)), 36, 10), 0, 0 - strlen(self::COURSE_ID_HASH_POSTFIX));
    	return  $val;
    }

}
