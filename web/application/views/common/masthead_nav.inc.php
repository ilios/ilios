<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Includes-template.
 * Prints out the markup for the admin navigation.
 */
$tabs = array(
    array(
        'label' => t('general.terms.home'),
        'target' => 'dashboard_controller',
        'id' => 't-home'
    ),
    array(
        'label' => t('general.terms.programs'),
        'target' => 'program_management',
        'id' => 't-program'
    ),
    array(
        'label' => t('general.terms.instructors'),
        'target' => 'instructor_group_management',
        'id' => 't-instructor'
    ),
    array(
        'label' => t('general.phrases.learner_groups'),
        'target' => 'group_management',
        'id' => 't-learner'
    ),
    array(
        'label' => t('general.phrases.courses_and_sessions'),
        'target' => 'course_management',
        'id' => 't-course'
    )
);
?>
<nav id="topnav">
    <ul class="tabs clearfix">
<?php
foreach ($tabs as $tabItem) :
?>
        <li id="<?php echo $tabItem['id'];?>">
            <a href="<?php echo site_url() . '/' . $tabItem['target']; ?>"><?php echo $tabItem['label']; ?> </a>
        </li>
<?php
endforeach;
?>
    </ul>
</nav> <!-- end #topnav -->
