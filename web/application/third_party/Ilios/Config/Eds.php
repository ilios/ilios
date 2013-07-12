<?php

/**
 * Constants interface holding read-only configuration-defaults
 * specific to Enterprise Directory Services (EDS),
 * the campus directory at UCSF.
 */
interface Ilios_Config_Eds
{
	/**
	 * school code for SOM
	 * @var int
	 */
	const SCHOOL_OF_MEDICINE_ID = 85;

	/**
	 * school code for SOD
	 * @var int
	 */
	const SCHOOL_OF_DENTISTRY_ID = 75;

	/**
	 * school code for SOP
	 * @var int
	 */
	const SCHOOL_OF_PHARMACY_ID = 94;

	/**
	 * school code for SON
	 * @var int
	 */
	const SCHOOL_OF_NURSING_ID = 90;
}
