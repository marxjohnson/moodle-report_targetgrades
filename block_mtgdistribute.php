<?php
/**
 * Defines the class for displaying the block itself.
 *
 * @package report_targetgrades
 * @author Mark Johnson <johnsom@tauntons.ac.uk>
 * @copyright Taunton's College, Southampton, UK 2010
 */

require_once($CFG->dirroot.'/blocks/mtgdistribute/lib.php');

/**
 * The block's class definition
 */
class report_targetgrades extends block_base {

    /**
     * Define the block's title and version number
     */
    function init() {
        $this->title = get_string('mtgdistribute', 'report_targetgrades');
        $this->version = 2010102700;
    }

    /**
     * Define the block's contents
     *
     * Display a link to alisdata.php. If ALIS data has been uploaded already,
     * also display a link to distribute.php, otherwise display an error message.
     *
     * @global object $CFG The global config object
     * @return string The content of the block
     */
    function get_content() {

        global $CFG;
        $this->content->footer = '';
        $this->config = get_config('block/mtgdistribute');
        if(empty($this->config->selected)) {
            $this->config->selected = ',';
        }

        if(isset($this->config->roles) && isset($this->config->categories)) {
            $roles = explode(',', $this->config->roles);
            $categories = explode(',', $this->config->categories);
            if(!empty($this->config->gcse_field) && !empty($roles) && !empty($categories)) {

                $this->content->text = '<a href="'.$CFG->wwwroot.'/blocks/mtgdistribute/alisdata.php">'.get_string('uploadalis', 'report_targetgrades').'</a><br />';
                if(get_records('mtgdistribute_alisdata')) {
                    $this->content->text .= '<a href="'.$CFG->wwwroot.'/blocks/mtgdistribute/distribute.php">'.get_string('mtgdistribute', 'report_targetgrades').'</a>';
                } else {
                    $this->content->text .= get_string('needsalis', 'report_targetgrades');
                }

            } else {
                $this->content->text = get_string('needsconfig', 'report_targetgrades');
            }

        } else {
            $this->content->text = get_string('needsconfig', 'report_targetgrades');
        }

        return $this->content;

    }

    function has_config() {
        return true;
    }

    function is_empty() {

        if (empty($this->instance->pinned)) {
            $context = get_context_instance(CONTEXT_BLOCK, $this->instance->id);
        } else {
            $context = get_context_instance(CONTEXT_SYSTEM); // pinned blocks do not have own context
        }

        if (!has_capability('block/mtgdistribute:view', $context) ) {
            return true;
        }

        $this->get_content();
        return(empty($this->content->text) && empty($this->content->footer));
    }

}

?>