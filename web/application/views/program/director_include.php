<?php

include(getServerFilePath('views') . 'common/director_picker_include.php');

?>

<script type="text/javascript">

	ilios.common.picker.director.getOwningModelForDirectorPicker = function (dialog) {
	 	var containerNumber = dialog.containerNumber;

	 	return ilios.pm.currentProgramModel.getProgramYearForContainerNumber(containerNumber);
	};

</script>
