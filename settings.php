<?php
/**
 * Defines the fields for the settings page
 *
 * @package block_mtgdistribute
 * @author Mark Johnson <johnsom@tauntons.ac.uk>
 * @copyright Taunton's College, Southampton, UK 2010
 */

$fields = array('',
    'shortname' => get_string('shortname'),
    'fullname' => get_string('fullname'),
    'idnumber' => get_string('idnumber'));

$roles = get_records('role');
foreach($roles as $key => $role) {
    $roles[$key] = $role->name;
}

$categories = get_records('course_categories');
foreach ($categories as $key => $category) {
    $categories[$key] = $category->name;
}

$infofields = get_records('user_info_field');
$userfields = array('');
foreach($infofields as $infofield) {
    $userfields[$infofield->shortname] = $infofield->name;
}

/**
 * Defines the number of characters to use for the pattern that matches courses
 * to a set of ALIS statistics
 */
$settings->add(new admin_setting_configtext('group_length', get_string('group_length', 'block_mtgdistribute'), '', '', PARAM_NUMBER));
$settings->settings->group_length->plugin='block/mtgdistribute';

/**
 * Defines the field to use for the pattern that matches courses against a set
 * of ALIS statistics
 */
$settings->add(new admin_setting_configselect('group_field', get_string('group_field', 'block_mtgdistribute'), get_string('group_fielddesc', 'block_mtgdistribute'), '', $fields));
$settings->settings->group_field->plugin='block/mtgdistribute';

/**
 * Defines the user field where the average GCSE scrore is stored
 */
$settings->add(new admin_setting_configselect('gcse_field', get_string('gcse_field', 'block_mtgdistribute'), get_string('gcse_fielddesc', 'block_mtgdistribute'), '', $userfields));
$settings->settings->gcse_field->plugin='block/mtgdistribute';

/**
 * Defines the roles to which target grades should be distributed
 */
$settings->add(new admin_setting_configmulticheckbox('roles', get_string('roles', 'block_mtgdistribute'), get_string('rolesdesc', 'block_mtgdistribute'), array('5'), $roles));
$settings->settings->roles->plugin='block/mtgdistribute';

/**
 * Defines the course categories that should be made available for distribution
 */
$settings->add(new admin_setting_configmulticheckbox('categories', get_string('categories', 'block_mtgdistribute'), get_string('categoriesdesc', 'block_mtgdistribute'), $categories, $categories));
$settings->settings->categories->plugin='block/mtgdistribute';

/**
 * Defines the field that should be matched to the regex to find which courses
 * should NOT be available for distribution
 */
$settings->add(new admin_setting_configselect('exclude_field', get_string('exclude_field', 'block_mtgdistribute'), '', '', $fields));
$settings->settings->exclude_field->plugin='block/mtgdistribute';

/**
 * Defines the regex that the above field should be matched against to find
 * courses which should NOT be available for distribution
 */
$settings->add(new admin_setting_configtext('exclude_regex', get_string('exclude_regex', 'block_mtgdistribute'), get_string('exclude_regexdesc', 'block_mtgdistribute'), ''));
$settings->settings->exclude_regex->plugin='block/mtgdistribute';

?>