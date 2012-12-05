<div id="viewbar" class="clearfix">
<?php
   if (! empty($viewbar_title)) :
?>
    <h1 id="view-current"><?php echo "$viewbar_title" ?>
<?php
     if (isset($available_schools)) {
?>
    <form>
      <select id="view-switch" name="schoolselect" onchange="this.form.submit();">
        <option selected="selected" class="selected"> <?php echo $select_school_string?> </option>
<?php
       foreach ( $available_schools as $schoolId => $schoolTitle ) {
?>
        <option value="<?php echo $schoolId ?>"
          <?php echo ($schoolId == $selected_school_id) ? 'disabled="disabled" class="checked"' : ''?> >
          <?php echo $schoolTitle?>
        </option>
<?php
       }
?>
      </select>
    </form>
<?php
     }
?>
    </h1>
<?php
   endif;
?>
</div>
