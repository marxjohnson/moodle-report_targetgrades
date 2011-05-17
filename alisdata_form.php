<?php
/**
 * Defines the form for uploading ALIS data
 *
 * @package report_targetgrades
 * @author Mark Johnson <johnsom@tauntons.ac.uk>
 * @copyright Taunton's College, Southampton, UK 2010
 */

require_once($CFG->libdir.'/formslib.php');

/**
 * The upload form's class for uploading ALIS data
 */
class alisdata_upload_form extends moodleform {

    public function definition() {
        $mform    =& $this->_form;
        $mform->addElement('file', 'equationsfile', get_string('equationsfile', 'report_targetgrades'));
        $mform->addElement('static', 'filedesc', '', get_string('equationsfiledesc', 'report_targetgrades'));
        $mform->addElement('submit', 'upload', get_string('upload'));
    }
}

?>
