<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

include(getServerFilePath('views') . 'common/director_picker_include.php');
?>

<script type="text/javascript">

    ilios.common.picker.director.getOwningModelForDirectorPicker = function (dialog) {
        return ilios.cm.currentCourseModel;
    };

</script>
