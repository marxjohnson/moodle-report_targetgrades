<?php
/**
 * Library of functions, constants and classes for Target Grade distribution
 *
 * @package report_targetgrades
 * @author Mark Johnson <johnsom@tauntons.ac.uk>
 * @copyright Taunton's College, Southampton, UK 2010
 */

/**
 * These constants store the strings used by ALIS to describe each type of
 * qualification that statistics are available for.
 */
define('ALIS_GCSE', 'GCSE');
define('ALIS_ADVANCED_GCE', 'Advanced GCE');
define('ALIS_ADVANCED_GCE_DOUBLE', 'Advanced GCE (Double Award)');
define('ALIS_ADVANCED_SUBSIDIARY_GCE', 'Advanced Subsidiary GCE');
define('ALIS_ADVANCED_SUBSIDIARY_GCE_DOUBLE', 'Advanced Subsidiary GCE (Double Award)');
define('ALIS_INTERMEDIATE_GNVQ', 'Intermediate GNVQ');
define('ALIS_IB_STANDARD', 'IB Standard');
define('ALIS_IB_HIGHER', 'IB Higher');
define('ALIS_CACHE_L3_DIPLOMA', 'CACHE Level 3 Diploma');
define('ALIS_OCR_NATIONAL_CERTIFICATE', 'OCR National Certificate');
define('ALIS_OCR_NATIONAL_DIPLOMA', 'OCR National Diploma');
define('ALIS_BTEC_NATIONAL_AWARD', 'BTEC National Award');
define('ALIS_BTEC_NATIONAL_CERTIFICATE', 'BTEC National Certificate');
define('ALIS_BTEC_NATIONAL_DIPLOMA', 'BTEC National Diploma');
define('ALIS_BTEC_FIRST_DIPLOMA', 'BTEC First Diploma');


/**
 * These constants define the various grade scales required for the above
 * qualifications. Only unique scales are stored here, e.g. the CACHE L3 diploma
 * can use MTG_SCALE_ADVANCED_SUBSIDIARY_GCE as they are the same.
 */
define('MTG_SCALE_GCSE', 'U,G,F,E,D,C,B,A,A*');
define('MTG_SCALE_ADVANCED_GCE', 'U,E,D,C,B,A,A*');
define('MTG_SCALE_ADVANCED_SUBSIDIARY_GCE', 'U,E,D,C,B,A,A*');
define('MTG_SCALE_BTEC_AWARD', 'Fail,Pass,Merit,Distinction');
define('MTG_SCALE_BTEC_CERTIFICATE', 'Fail,PP,MP,MM,DM,DD');
define('MTG_SCALE_BTEC_DIPLOMA', 'Fail,PPP,MPP,MMP,MMM,DMM,DDM,DDD');
define('MTG_SCALE_ADVANCED_GCE_DOUBLE', 'U,EE,DE,DD,CD,CC,BC,BB,AB,AA,A*A,A*A*');
define('MTG_SCALE_ADVANCED_SUBSIDIARY_GCE_DOUBLE', 'U,EE,DE,DD,CD,CC,BC,BB,AB,AA,A*A,A*A*');
define('MTG_SCALE_IB', '1,2,3,4,5,6,7');

/**
 * Returns the grade scale for the provided qualification type.
 * 
 * @param string $qualtype 
 */
function mtgdistribute_get_scale($qualtype) {
    switch ($qualtype) {
        case ALIS_GCSE:
            return MTG_SCALE_GCSE;
            break;

        case ALIS_ADVANCED_GCE:
            return MTG_SCALE_ADVANCED_GCE;
            break;

        case ALIS_ADVANCED_GCE_DOUBLE:
            return MTG_SCALE_ADVANCED_GCE_DOUBLE;
            break;

        case ALIS_ADVANCED_SUBSIDIARY_GCE:
            return MTG_SCALE_ADVANCED_SUBSIDIARY_GCE;
            break;

        case ALIS_ADVANCED_SUBSIDIARY_GCE_DOUBLE:
            return MTG_SCALE_ADVANCED_SUBSIDIARY_GCE_DOUBLE;
            break;

        case ALIS_INTERMEDIATE_GNVQ:
            // TODO: Work out what this should be
            return false;
            break;

        case ALIS_IB_STANDARD:
            return MTG_SCALE_IB;
            break;

        case ALIS_IB_HIGHER:
            return MTG_SCALE_IB;
            break;

        case ALIS_CACHE_L3_DIPLOMA:
            return MTG_SCALE_ADVANCED_SUBSIDIARY_GCE;
            break;

        case ALIS_OCR_NATIONAL_CERTIFICATE:
            return MTG_SCALE_BTEC_AWARD;
            break;

        case ALIS_OCR_NATIONAL_DIPLOMA:
            return MTG_SCALE_BTEC_CERTIFICATE;
            break;

        case ALIS_BTEC_NATIONAL_AWARD:
            return MTG_SCALE_BTEC_AWARD;
            break;

        case ALIS_BTEC_NATIONAL_CERTIFICATE:
            return MTG_SCALE_BTEC_CERTIFICATE;
            break;

        case ALIS_BTEC_NATIONAL_DIPLOMA:
            return MTG_SCALE_BTEC_DIPLOMA;
            break;

        case ALIS_BTEC_FIRST_DIPLOMA:
            return MTG_SCALE_BTEC_AWARD;
            break;
    }
}

/**
 * Work out if a provided course (fetched using {@link mtgdistribute_get_courses_with_qualtype()})
 * is an ALIS course.
 *
 * Checks that the qualtype is one of the qualtype constants, then checks that
 * there is ALIS data linked to the course.
 *
 * @global object $CFG Global config object
 * @param object $course a course fetched with mtgdistribute_get_courses_with_qualtype
 * @return bool
 */
function mtgdistribute_isalis($course){
    global $CFG, $DB;
    $types = array(ALIS_GCSE,
        ALIS_ADVANCED_GCE,
        ALIS_ADVANCED_GCE_DOUBLE,
        ALIS_ADVANCED_SUBSIDIARY_GCE,
        ALIS_ADVANCED_SUBSIDIARY_GCE_DOUBLE,
        ALIS_INTERMEDIATE_GNVQ,
        ALIS_IB_STANDARD,
        ALIS_IB_HIGHER,
        ALIS_CACHE_L3_DIPLOMA,
        ALIS_OCR_NATIONAL_CERTIFICATE,
        ALIS_OCR_NATIONAL_DIPLOMA,
        ALIS_BTEC_NATIONAL_AWARD,
        ALIS_BTEC_NATIONAL_CERTIFICATE);
    // Firstly, is the qualtype one of the current ALIS qualtypes?
    if(in_array($course->qualtype, $types)) {
        // If it is, do we have ALIS data for this particular subject?
        $select = 'SELECT * ';
        $from = 'FROM {report_targetgrades_patterns} AS p
            JOIN {report_targetgrades_alisdata} AS d ON p.alisdataid = d.id ';
        $where = 'WHERE p.pattern = ?';
		$params = array($course->pattern);
        if($DB->get_record_sql($select.$from.$where, $params)) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * Append an asterisk to the course's fullname if it doesn't have ALIS data
 *
 * Used as a callback for array_walk to iterate over an array of courses fetched
 * with {@link mtgdistribute_get_courses_with_qualtype()}, check if each one has
 * ALIS data linked to it, and appends and asterisk if it's not.
 *
 * @global object $CFG Global config object
 * @param object $course A course fetched with mtgdistribute_get_courses_with_qualtype
 * @return bool true if the course has ALIS config
 */
function mtgdistribute_hasconfig(&$course){
    global $CFG, $DB;
    $select = 'SELECT * ';
    $from = 'FROM {report_targetgrades_patterns} AS p
        JOIN {report_targetgrades_alisdata} AS d ON p.alisdataid = d.id ';
    $where = 'WHERE p.pattern = ?';
	$params = array($course->pattern);
    if($DB->get_record_sql($select.$from.$where, $params)) {
        return true;
    } else {
        $course->fullname .= '*';
    }    
}

/**
 * Add or remove an array of course IDs from the blocks configuration
 *
 * Takes an array of course IDs and adds or removes them from the "selected"
 * config property as appropriate
 *
 * @param array $selected array of ids to add or remove
 * @param string $op 'add' or 'remove'
 */
function mtgdistribute_saveselected($selected, $op) {
    $config_selected = get_config('block/mtgdistribute', 'selected');
    if(!empty($config_selected)) {
        $config_selected = explode(',', $config_selected);
        if($op == 'add') {
            $config_selected = array_merge($config_selected, $selected);
        } else if ($op == 'remove') {
            $config_selected = array_diff($config_selected, $selected);
        }
    } else {
        $config_selected = $selected;
    }
    $config_selected = implode(',', $config_selected);
    set_config('selected', $config_selected, 'block/mtgdistribute');
}

/**
 * Clear the selected config attribute
 */
function mtgdistribute_clearselected() {
    set_config('selected', '', 'block/mtgdistribute');
}

/**
 * Get records for all the courses in the database with ALIS qualtypes and patterns
 *
 * Fetches the course ID, the pattern as defined in the block's configuration,
 * and the ALIS qualifcation type the course belongs to if this has been
 * configured.
 *
 * @global object $config The block's config object
 * @global object $CFG The global config object
 * @param array $extraconditions any extra conditions to add to the where clause
 * @return array of course records
 */
function mtgdistribute_get_courses_with_qualtype($wherefields = array(), $wherevalues = array()) {
    global $config, $CFG, $DB;
	
    /*if(!empty($config->exclude_field) && !empty($config->exclude_regex)) {
        $args = array($config->exclude_field, $config->exclude_regex);
        $conditions[] = vsprintf('c.%1$s NOT REGEXP "%2$s"', $args);
    }*/

    if(!empty($config->category)) {
        $wherefields[] = 'c.category';
		$wherevalues[] = $config->category;
    }

     $select = 'SELECT c.id, ';
    if(!empty($config->group_field) && !empty($config->group_length)) {
        $args = array($config->group_field, $config->group_length);
        $select .= vsprintf('LEFT(c.%1$s, %2$d) AS pattern, ', $args);
        $from = vsprintf('FROM {course} AS c
                    LEFT JOIN {report_targetgrades_patterns} AS p
                        ON LEFT(c.%1$s, %2$d) = CAST(p.pattern AS CHAR)
                    LEFT JOIN {report_targetgrades_alisdata} AS d ON p.alisdataid = d.id
                    LEFT JOIN {report_targetgrades_qualtype} AS q ON d.qualtypeid = q.id ', $args);
    } else {
        $select .= 'c.shortname AS pattern, ';
        $from = 'FROM {course} AS c
                    LEFT JOIN {report_targetgrades_alisdata} AS d ON p.alisdataid = d.id
                    LEFT JOIN {reprot_targetgrades_alisdata} AS q ON d.qualtypeid = q.id ';
    }
    $select .= 'c.shortname, c.fullname, q.name AS qualtype ';
    $where = '';

    foreach ($wherefields as $key => $field) {
        if ($key == 0) {
            $where .=('WHERE '.$field.' = ? ');
        } else {
            $where .= ('AND '.$field.' = ?');
        }
    }
    return $DB->get_records_sql($select.$from.$where, $wherevalues); // Get a list of all courses matching the preferences
}

/**
 * Returns the record for a single course with pattern and qualtype
 *
 * Calls {@link mtgdistribute_get_courses_with_qualtype()} with an extra
 * condition to return the record for one specific course.
 *
 * @param int $id ID of the course
 * @return object the record for the course matching the ID
 */
function mtgdistribute_get_course_with_qualtype($id) {
    $results = mtgdistribute_get_courses_with_qualtype(array('c.id'), array($id));
    return $results[$id];
}

/**
 * Calculates a minimum target grade for a particular course based on
 * an average GCSE score
 *
 * @param $course text either a full classcode or just the first 5 characters
 * (will take the first 5 characters anyway)
 * @param $avgcse int student's average gcse score
 *
 * @return integer relating to the grade on a scale or false if fails
 */
function mtgdistribute_calculate_mtg($student, $course){
    global $CFG;
    $select = 'SELECT name, gradient, intercept ';
    $from = 'FROM {mtgdistribute_patterns} AS p
        JOIN {mtgdistribute_alisdata} AS d ON p.alisdataid = d.id ';
    $where = 'WHERE p.pattern = ?';
	$params = array($course->pattern);
	
    if($alis = $DB->get_record_sql($select.$from.$where, $params)) {
        $points = ($student->avgcse * $alis->gradient)+$alis->intercept;

        // Calculate UCAS points using ALIS formula

        switch($course->qualtype) {
            // Calculate grade value based on course type

            case ALIS_ADVANCED_GCE: // A2
                // Divide points score by 2
                // Divide by 10
                // Round Up
                $mtg = ceil((($points/2))/10);
                break;

            case ALIS_ADVANCED_GCE_DOUBLE: // A2 Double Award
                // Divide points score by 2
                // Minus 20
                // Divide by 10
                // Round Up
                $mtg = ceil((($points/2)-20)/10);
                break;

            case ALIS_ADVANCED_SUBSIDIARY_GCE: // AS
                // Minus 20
                // Divide by 10
                // Round Up
                $mtg = ceil($points/10);

                break;           

            case ALIS_ADVANCED_SUBSIDIARY_GCE_DOUBLE: // AS Double Award
                // Divide by 10
                // Round Up
                $mtg = ceil(($points-20)/10);
                break;

            case ALIS_BTEC_NATIONAL_DIPLOMA: // BTEC Diploma
                // Add 30
                // Minus 100
                // Divide by 40
                // Round up
                // Add 1 (scale includes Fail)
                $mtg = ceil((($points+20)-100)/40)+1;
                break;

            case ALIS_BTEC_NATIONAL_CERTIFICATE: // BTEC Certificate
                $mtg = ceil($points/40);
                break;

            case ALIS_BTEC_NATIONAL_AWARD: // BTEC Award            
                // Add 10
                // Divide by 40
                // Round up
                // Add 1 (scale includes Fail)
                $mtg = ceil($points/40)+1;
                if($mtg > 4) {
                   $mtg = 4;
                }
                break;

            case ALIS_CACHE_L3_DIPLOMA: // CACHE L3 Diploma
                $mtg = ceil($points/60);
                break;

             // These all provide a raw number on the scale, rather than UCAS
            // tariff points.
            case ALIS_IB_STANDARD: // IB
            case ALIS_IB_HIGHER: // IB            
            case ALIS_BTEC_FIRST_DIPLOMA: // BTEC 1st
            case ALIS_OCR_NATIONAL_DIPLOMA: // OCR Diploma
            case ALIS_OCR_NATIONAL_CERTIFICATE: // OCR Certificate
                // Add 0.5
                // Round up
                $mtg = ceil($points+0.5);
                if ($course->qualtype == ALIS_BTEC_FIRST_DIPLOMA
                        || $course->qualtype == ALIS_OCR_NATIONAL_DIPLOMA
                        ||  $course->qualtype == ALIS_OCR_NATIONAL_CERTIFICATE) {
                    // For these 3, add an extra one as the scale includes Fail
                    $mtg += 1;
                }
                break;

            case ALIS_GCSE: // GCSE
                // Always a C, since if they're in FE and taking a GCSE it's becuase
                // they failed and are trying to pass.
                $mtg = 6;
                break;

            default:
                $mtg = '';

        }

        if ($mtg !== '' && $mtg <= 1) {
            $mtg = 2;
        }
        $alis = array();
        $alis['number'] = $points;
        $alis['grade'] = $mtg;
        return $alis;

    } else {
        throw new no_mtg_for_student_exception($student->id);
    }
    
}

/**
 * Builds a set of options to select pattens to apply ALIS stats to
 *
 * @global object $CFG The global config object
 * @global object $config The block's config object
 * @param string $default Optional, The pattern to select by default
 * @return string the options as HTML elements
 * @throws Exception if the exclusion regex shows a risk of ReDOS
 */
function mtgdistribute_build_pattern_options($default = '') {

    global $CFG, $config;
    $conditions = array();
    if(preg_match('/(.+?\.?[*+].*?)[*+]/', $config->exclude_regex)) {
        throw new unsafe_regex_exception();
    } else {
        if(!empty($config->exclude_field) && !empty($config->exclude_regex)) {
            $conditions[] = $config->exclude_field.' NOT REGEXP "'.$config->exclude_regex.'"';
        }
    }

    if(!empty($config->categories)) {
        $conditions[] = 'c.category IN ('.$config->categories.')';
    }

    $select = 'SELECT DISTINCT ';
    if(!empty($config->group_field) && !empty($config->group_length)) {
        $select .= 'LEFT('.$config->group_field.', '.$config->group_length.') AS pattern ';
    } else {
        $select .= 'shortname AS pattern ';
    }

    $from = 'FROM {course} AS c ';

    $where = '';
    foreach ($conditions as $key => $condition) {
        if ($key == 0) {
            $where .= 'WHERE ';
        } else {
            $where .= 'AND ';
        }
        $where .= $condition.' ';
    }

    $order = 'ORDER BY pattern';

    $options = '<option value=""></option>';
    if($patterns = $DB->get_records_sql($select.$from.$where.$order)) {
        foreach($patterns as $pattern) {
            $options .= '<option value="'.$pattern->pattern.'" ';
            if ($pattern->pattern == $default) {
                $options .= 'selected="selected" ';
            }
            $options .= '>'.$pattern->pattern.'</option>';
        }
    }
    return $options;
}

/**
 * Re-sorts the gradebook to put all MTG grade items first.
 *
 * Gets all of the grade items for the specifed course. Iterates over the array
 * of items and moves the MTG items to the front of the array. Then does a
 * second pass to renumber all the sortorders to make the items sequential from
 * 2 upwards (1 will be the course item).
 *
 * @param object $course Database record for course, containing the id.
 */
function mtgdistribute_sort_gradebook($course) {

    global $CFG, $DB;
    require_once $CFG->dirroot.'/grade/lib.php';
    require_once $CFG->dirroot.'/grade/edit/tree/lib.php';
    $gtree = new grade_tree($course->id, false, false);
	
	$fields = array('alis_avgcse', 'alis_alisnum', 'alis_alis', 'alis_mtg', 'alis_cpg');	
	$params = array($course->id);
	list($in_params, $in_sql) = $DB->get_in_or_equal($params);
	$params = array_merge($params, $in_params);
    $where = 'courseid = ? AND idnumber '.$in_sql;
    $gradeitems = $DB->get_records_select('grade_items', $where, $params, 'itemnumber DESC');
    $courseitem = $DB->get_record('grade_items', array('courseid' => $course->id, 'itemtype' => 'course'));
    //$mtgitems = count_records_select('grade_items', $where);

    // First, move the MTG grade items to the front
    $offset = 0;
    foreach($gradeitems as $item) {

        if (!$element = $gtree->locate_element('i'.$item->id)) {
            print_error('invalidelementid');
        }
        $object = $element['object'];

        $moveafter = 'c'.$courseitem->iteminstance;
        $first = 1; // If First is set to 1, it means the target is the first child of the category $moveafter

        if(!$after_el = $gtree->locate_element($moveafter)) {
            print_error('invalidelementid');
        }

        $after = $after_el['object'];
        $sortorder = $after->get_sortorder();

        if (!$first) {
            $parent = $after->get_parent_category();
            $object->set_parent($parent->id);
        } else {
            $object->set_parent($after->id);
        }

        $object->move_after_sortorder($sortorder);

    }


}

/**
 * Prints tabs for navigating the block's pages
 *
 * Prints a tab linking to alisdata.php, and one linking to distribute.php.
 * If ALIS data hasn't been uploaded yet, the link to distribute.php will not
 * be displayed.
 *
 * @global object $CFG Global config object
 * @param int $selected The ID of the tab to select
 */
function mtgdistribute_print_tabs($selected) {
    global $CFG, $DB;

    $tabs = array();
    $tabs[] = new tabobject(1,
            $CFG->wwwroot.'/blocks/mtgdistribute/alisdata.php',
            get_string('alisdata', 'report_targetgrades'));
    if($DB->get_records('mtgdistribute_alisdata')) {
        $tabs[] = new tabobject(2,
                $CFG->wwwroot.'/blocks/mtgdistribute/distribute.php',
                get_string('mtgdistribute', 'report_targetgrades'));
    }
    print_tabs(array($tabs), $selected);
}

/**
 * A skeleton grade record
 */
abstract class mtg_item_grade {

    public   $courseid;
    public   $categoryid;
    public   $gradetype;
    public   $grademax;
    public   $grademin;
    public   $hidden;
    public   $itemnumber;
    public   $itemname;
    public   $locked;
    public   $idnumber;

    /**
     * Define common attributes for the subclasses
     *
     * @param int $courseid The course the grade item belongs to
     * @param int $categoryid The category the grade item belongs to
     */
    protected function  __construct($courseid, $categoryid) {
        $this->courseid = $courseid;
        $this->categoryid = $categoryid;
        $this->hidden = 0;
        $this->grademin = 0;
        $this->idnumber = '';
        $this->timecreated = time();
        $this->timemodified = time();
    }
}

/**
 * A course gradeitem
 */
class mtg_item_course extends mtg_item_grade {
    public $timecreated;
    public $timemodified;
    public $sortorder;

    /**
     * Sets all the attributes to those of a course grade item
     *
     * @param int $courseid The course the grade item belongs to
     * @param int $categoryid The category the grade item belongs to
     */
    public function  __construct($courseid, $categoryid) {
        $this->courseid = $courseid;
        $this->instanceid = $categoryid;
        $this->itemtype = 'course';
        $this->locked = 0;
        $this->hidden = 0;
        $this->gradetype = 0;
        $this->grademax = 0;
        $this->grademin = 0;
        $this->sortorder = 1;
        $this->idnumber = 'alis_mtg';
    }
}

/**
 * An average GCSE grade item
 */
class mtg_item_avgcse extends mtg_item_grade {
    public $decimals = 2;

    /**
     * Sets all the attributes to those of an Average GCSE item
     *
     * @param int $courseid The course the grade item belongs to
     * @param int $categoryid The category the grade item belongs to
     */
    public function __construct($courseid, $categoryid) {
        parent::__construct($courseid, $categoryid);
        $this->itemname = get_string('item_avgcse', 'report_targetgrades');
        $this->gradetype = 1;
        $this->grademax = 10;
        $this->grademin = 0;
        $this->locked = time();
        $this->idnumber = 'alis_avgcse';
        $this->itemnumber = 1;

    }
    
}

/**
 * An ALIS number grade item
 */
class mtg_item_alisnum extends mtg_item_grade {

    public $decimals;

    /**
     * Sets all the attributes to those of an ALIS number item
     *
     * @param int $courseid The course the grade item belongs to
     * @param int $categoryid The category the grade item belongs to
     */
    public function __construct($courseid, $categoryid) {
        parent::__construct($courseid, $categoryid);
        $this->itemname = get_string('item_alisnum', 'report_targetgrades');
        $this->gradetype = 1;
        $this->grademax = 360;
        $this->grademin = 0;
        $this->decimals = 0;
        $this->hidden = 1;
        $this->locked = time();
        $this->idnumber = 'alis_alisnum';
        $this->itemnumber = 2;
    }

}

/**
 * An ALIS grade item
 */
class mtg_item_alis extends mtg_item_grade {

    public   $scaleid;

    /**
     * Sets the attributes to those of an ALIS grade item
     *
     * @param int $courseid The course the grade item belongs to
     * @param int $categoryid The category the grade item belongs to
     */
    public function __construct($courseid, $categoryid) {
        parent::__construct($courseid, $categoryid);
        $this->itemname = get_string('item_alis', 'report_targetgrades');
        $this->hidden = 1;
        $this->locked = time();
        $this->gradetype = 0;
        $this->grademax = 0;
        $this->scaleid = 0;
        $this->idnumber = 'alis_alis';
        $this->itemnumber = 3;
    }

    /**
     * Sets the grade scale based on the qualtype, or the default if none is set
     *
     * @param string $qualtype Must == one of the qualtype constants
     * @param int $default The ID of the default scale to use
     */
    public function set_scale($qualtype, $default = null) {
    	global $DB;
        $scale = $DB->get_record('scale', 'name', $qualtype.' MTG');
        if(!$scale) {
            if(!empty($default)) {
                $scale = $DB->get_record('scale', 'id', $default);
            } else {
                throw new Exception($qualtype);
            }
        }
        $this->gradetype = 2;
        $this->grademax = count($scale->scale);
        $this->scaleid = $scale->id;        
    }
}

/**
 * A Minimum Target Grade item
 */
class mtg_item_mtg extends mtg_item_alis {

    /**
     * Sets the attributes to those of an MTG grade item
     *
     * @param int $courseid The course the grade item belongs to
     * @param int $categoryid The category the grade item belongs to
     */
    public function __construct($courseid, $categoryid) {
        parent::__construct($courseid, $categoryid);
        $this->itemname = get_string('item_mtg', 'report_targetgrades');
        $this->hidden = 0;
        $this->locked = 0;
        $this->idnumber = 'alis_mtg';
        $this->itemnumber = 4;
    }
}

/**
 * A Minimum Target Grade item
 */
class mtg_item_cpg extends mtg_item_alis {

    /**
     * Sets the attributes to those of an MTG grade item
     *
     * @param int $courseid The course the grade item belongs to
     * @param int $categoryid The category the grade item belongs to
     */
    public function __construct($courseid, $categoryid) {
        parent::__construct($courseid, $categoryid);
        $this->itemname = get_string('item_cpg', 'report_targetgrades');
        $this->hidden = 0;
        $this->locked = 0;
        $this->idnumber = 'alis_cpg';
        $this->itemnumber = 5;
    }
}

/**
 * A grade record
 */
class mtg_grade {
    public $itemid;
    public $userid;
    public $rawgrade;
    public $finalgrade;
    public $timecreated;
    public $timemodified;


    /**
     * Creates a record for the grade item
     *
     * @param int $itemid The ID of the item the grade is for
     * @param int $user The ID of the user the grade is for
     * @param int $grade The grade as an index on the grade scale
     */
    public function __construct($itemid, $user, $grade) {
        $this->itemid = $itemid;
        $this->userid = $user->id;
        $this->rawgrade = $grade;
        $this->finalgrade = $grade;
        $this->timecreated = time();
        $this->timemodified = time();
    }
}

/**
 * Used to flag up when a class has no students
 */
class no_students_exception extends Exception {}

/**
 * Used to flag up when a student has no Average GCSE data
 */
class no_data_for_student_exception extends Exception {}

/**
 * Used to flag when a student's MTG calcucation failed for some reason
 */
class no_mtg_for_student_exception extends Exception {}

/**
 * Used to flag when a course has no ALIS data configured
 */
class no_config_for_course_exception extends Exception {}

/**
 * Used to return the ID of a grade item if one already exists for the given
 * criteria.
 */
class grade_item_exists_exception extends Exception {
    private $id;

    public function __construct($message = "", $id = 0, $code = 0) {
        parent::__construct($message, $code);
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }
}

/**
 * Used to flag when an regex with a risk of ReDOS is detected
 */
class unsafe_regex_exception extends Exception {
    public function __construct() {
        parent::__construct('unsaferegex');
    }
}

?>
