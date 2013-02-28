<?php

/**
 * Ilios extension to CodeIgniter's default Upload class.
 *
 * Since CI2 is cramming mime-"magic" down our throat, which does very poorly
 * in identifying mime-types of modern MS Office files, we need to provide means
 * to correct mismatched mime-types.
 * We do this here.
 * It's a rather ham-handed solution but it works for our needs right now.
 * [ST 02/25/2013]
 *
 */
class Ilios_CI_Upload extends CI_Upload
{
    /**
     * The mime-type correction map.
     * Takes a file suffix/"wrong" mime-type as key and the correct mime-type as value.
     * Treat this as read-only.
     * @var array
     */
    protected $_correctedMimeTypesMap = array(
        '.docm|application/msword' => 'application/vnd.ms-word.document.macroEnabled.12',
        '.docx|application/msword' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        '.dotm|application/msword' => 'application/vnd.ms-word.template.macroEnabled.12',
        '.dotx|application/msword' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        '.potm|application/vnd.ms-powerpoint' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
        '.potx|application/vnd.ms-powerpoint' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
        '.pot|application/msword' => 'application/vnd.ms-powerpoint',
        '.ppam|application/vnd.ms-powerpoint' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
        '.ppa|application/msword' => 'application/vnd.ms-powerpoint',
        '.ppsm|application/vnd.ms-powerpoint' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
        '.ppsx|application/vnd.ms-powerpoint' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        '.pps|application/msword' => 'application/vnd.ms-powerpoint',
        '.pptm|application/vnd.ms-powerpoint' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
        '.pptx|application/vnd.ms-powerpoint' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        '.ppt|application/msword' => 'application/vnd.ms-powerpoint',
        '.xlam|application/vnd.ms-excel' => 'application/vnd.ms-excel.addin.macroEnabled.12',
        '.xlsb|application/vnd.ms-excel' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
        '.xlsm|application/vnd.ms-excel' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
        '.xlsx|application/vnd.ms-excel' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        '.xltm|application/vnd.ms-excel' => 'application/vnd.ms-excel.template.macroEnabled.12',
        '.xltx|application/vnd.ms-excel' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template'
    );

    /**
     * Performs a map lookup on a given file suffix and mime-type.
     * Returns the corrected mimetype if a match was found in the map, or the given mime-type on no-match.
     * @param string $fileExtension the file extension (including the leading dot)
     * @param string $mimeType the mime-type
     * @return string the (corrected) mime-type
     */
    public function getCorrectedMimeType ($fileExtension, $mimeType)
    {
        $key = strtolower($fileExtension . '|' . $mimeType);
        if (array_key_exists($key, $this->_correctedMimeTypesMap)) {
            return $this->_correctedMimeTypesMap[$key];
        }
        // no match? we assume that no correction was needed, and return the given mime-type.
        return $mimeType;
    }
}