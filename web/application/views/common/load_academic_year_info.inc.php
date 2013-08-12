<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Includes template.
 * Populates the academic year map on page load.
 * Put this inside the javascript-block at the bottom of each page template where applicable.
 *
 * Expects the following template variables to be present:
 *
 * $academic_years ... a JSON-formatted representation of academic years by their owning school.
 */
?>

// load school competencies
YAHOO.util.Event.onDOMReady(function () {
    var s, map;
    var jsonStr = '<?php echo $academic_years; ?>';

    try {
        s = YAHOO.lang.JSON.parse(jsonStr);
        map = ilios.ay.convertSchoolCompetencyHierarchiesIntoLookupMap(s);
    } catch (e) {
        ilios.global.defaultAJAXFailureHandler(null, e);
        return;
    }

    if (map !== undefined) {
        ilios.ay.getAcademicYears(map);
    }
});
