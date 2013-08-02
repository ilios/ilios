<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Includes-template.
 *
 * Provides the academic year checking functionality as javascript
 *
 * @see application/views/course/course_manager.php
 *
 * DEPENDENCIES:
 *     YUI toolkit
 *     scripts/ilios_dom.js
 *     scripts/ilios_utilities.js
 */
?>
<script type="text/javascript">
//create an object with all the academic year dates for the current school id
var all_academic_years = {
 <?php
 	// in order to check dates against the academic year start and end dates,
    // we need to set get all the start and end dates for the current school id
    $all_academic_years_array = $this->academic_year->getAllAcademicYearsFromSchoolId();
    //get the total returned, so we can know when to end the object creation...
    $total_academic_years = count($all_academic_years_array);
    
 	//start the counter to test for the final value
 	$count = 1;
 	//loop through each year returned and add the object's properties...
 	foreach($all_academic_years_array as $academic_year => $properties) {
 	  
 	    //set variables and do transformations...
 		$academic_year_start_date = $properties['academic_year_start_date'];
 		$unix_start_date = strtotime($academic_year_start_date);
 		$formatted_start_date = date('F j, Y', $unix_start_date);
 		$academic_year_end_date = $properties['academic_year_end_date'];
 		$unix_end_date = strtotime($academic_year_end_date);
 		$formatted_end_date = date('F j, Y', $unix_end_date);
        
 		//output the object properties...
 		echo "	'{$academic_year}' : {\n";
 		echo "		'academic_year_start_date' : '{$academic_year_start_date}',\n";
 		echo "		'formatted_academic_year_start_date' : '{$formatted_start_date}',\n";
 		echo "		'unix_academic_year_start_date' : '{$unix_start_date}',\n";
 		echo "		'academic_year_end_date' : '{$academic_year_end_date}',\n";
 		echo "		'formatted_academic_year_end_date' : '{$formatted_end_date}',\n";
 		echo "		'unix_academic_year_end_date' : '{$unix_end_date}'\n";
 		echo "		}";
 		//if it's the last returned result, close the object...
 		echo ($count == $total_academic_years) ? "\n	};\n" : ",\n";
 		$count++;
 	}
 ?>
</script>
<script type="text/javascript">
var checkDateForAcademicYearOverlap = function (date) {

	//add the time to the date for conversion to unixtimestamp timestamp
	var datetime = (date + ' 12:00:00');
	//get the unix timestamp version of the date...
	var unix_date = (Date.parse(datetime) / 1000);

	//create and empty array to hold the year values
	var overlapping_years = [];

	//loop through the all_academic_years object
	//for the unix start and end dates
	for (var academic_year in all_academic_years) {
   		var obj = all_academic_years[academic_year];
   		for (var prop in obj) {
      		if(obj.hasOwnProperty(prop)){
      			//get the academic year start date in unixtime...
      			if(prop == 'unix_academic_year_start_date'){
      				var unix_start_date = obj[prop];
      			}
      			//and the academic year end date in unixtime...
      			if(prop == 'unix_academic_year_end_date'){
      				var unix_end_date = obj[prop];
      			}
        	}
   		}
		//cycle through all the years and check if they're between the two values
	    if (unix_date >= unix_start_date && unix_date <= unix_end_date) {
			//if they are add them to the return array...
		    overlapping_years.push(academic_year);
	    }
	}
	//return the array and function can check its (.lengh > 0) on the other end...
	return overlapping_years;
}
</script>
