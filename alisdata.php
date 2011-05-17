<?php
/**
 * Import ALIS statistics for target grade calculations
 *
 * Presents a form allowing a CSV file containing ALIS data (generated using
 * alis_pdf2csv.sh) to be uploaded. Once uploaded, a list of statistics is
 * displayed, allowing patterns to be defined for each set of statistics to be
 * applied to when grades are distributed.
 *
 * @package block_mtgdistribute
 * @author Mark Johnson <johnsom@tauntons.ac.uk>
 * @copyright Taunton's College, Southampton, UK 2010
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/blocks/mtgdistribute/alisdata_form.php');
require_once($CFG->dirroot.'/blocks/mtgdistribute/lib.php');
require_once($CFG->libdir.'/tablelib.php');

$savepatterns = optional_param('savepatterns', false, PARAM_BOOL);
$alispatterns = optional_param('alispatterns', array(), PARAM_CLEAN);
$addpattern = optional_param('addpattern', array());
$config = get_config('block/mtgdistribute');
$addfield = optional_param('addfield', null, PARAM_INT);

if ($savepatterns || !empty($addpattern)) {
    foreach($alispatterns as $alisid => $patterns) {
        foreach($patterns as $id => $pattern) {
            if ($alisdata = get_record('mtgdistribute_alisdata', 'id', $alisid)) {                
                if($patternrecord = get_record('mtgdistribute_patterns', 'id', $id)) {
                    if(empty($pattern)) {                        
                        delete_records('mtgdistribute_patterns', 'id', $id);
                    } else {
                        $patternrecord->pattern = $pattern;
                        update_record('mtgdistribute_patterns', $patternrecord);
                    }
                } else if (!empty($pattern)) {
                    $patternrecord = new stdClass;
                    $patternrecord->alisdataid = $alisid;
                    $patternrecord->pattern = $pattern;
                    $patternrecord->id = insert_record('mtgdistribute_patterns', $patternrecord);
                }
            }            
        }
    }
    $output = '<p>'.get_string('changessaved').'</p>';
    if (!empty($addpattern)) {
        header('Location: '.$CFG->wwwroot.'/blocks/mtgdistribute/alisdata.php?addfield='.key($addpattern).'#alis'.key($addpattern));
    }
}

$uploadform = new alisdata_upload_form();

if ($uploaddata = $uploadform->get_data()) {
    if($file = fopen($_FILES['equationsfile']['tmp_name'], 'r')) {
        $qualtype = false;
        $import->qualcount = 0;
        $import->subjectcount = 0;
        $import->updatecount = 0;
        while ($line = fgetcsv($file, 0, '|')) {

            // If there's only one column on this line, then it's a qualification heading
            if (count($line) == 1) {
                // Create a new qualtype record if there isn't one already.
                if(!$qualtype = get_record('mtgdistribute_qualtype', 'name', $line[0])) {

                    if(!$qualscale = get_record('scale', 'name', $line[0].' MTG')) {

                        if($scale = mtgdistribute_get_scale($line[0])) {
                            $qualscale = new stdClass;
                            $qualscale->name = $line[0].' MTG';
                            $qualscale->scale = $scale;
                            $qualscale->id = insert_record('scale', $qualscale);
                        }

                    }

                    if($qualscale) {
                        $qualtype = new stdClass;
                        $qualtype->name = $line[0];
                        $qualtype->scaleid = $qualscale->id;
                        $qualtype->id = insert_record('mtgdistribute_qualtype', $qualtype);
                        $import->qualcount++;
                    }
                }
                
            } else {
                // If we have a record for this course's qualtype
                if ($qualtype) {
                    if($subject = get_record('mtgdistribute_alisdata', 'name', $line[0], 'qualtypeid', $qualtype->id)) {
                        $subject->gradient = $line[2];
                        $subject->intercept = $line[3];
                        update_record('mtgdistribute_alisdata', $subject);
                        $import->updatecount++;
                    } else {
                        $subject = new stdClass;
                        $subject->name= $line[0];
                        $subject->gradient = $line[2];
                        $subject->intercept = $line[3];
                        $subject->qualtypeid = $qualtype->id;
                        insert_record('mtgdistribute_alisdata', $subject);
                        $import->subjectcount++;
                    }
                }
            }
        }
        $output = '<p>'.get_string('importoutput', 'block_mtgdistribute', $import).'</p>';
    }
}

$select = 'SELECT a.id, a.name AS subject, q.name AS qualification, a.gradient, a.intercept ';
$from = sprintf('FROM %1$salisdata AS a
    JOIN %1$squaltype AS q ON a.qualtypeid = q.id ', $CFG->prefix.'mtgdistribute_');
$order = 'ORDER BY q.name, a.name ASC';

if($alis_data = get_records_sql($select.$from.$order)) {

    $table = new flexible_table('alisdatatable');

    $table->define_columns(array('qualtype', 'name', 'pattern', 'gradient', 'intercept'));
    $table->define_headers(array(get_string('col_qualtype', 'block_mtgdistribute'),
            get_string('col_name', 'block_mtgdistribute'),
            get_string('col_pattern', 'block_mtgdistribute'),
            get_string('col_gradient', 'block_mtgdistribute'),
            get_string('col_intercept', 'block_mtgdistribute')));
    $table->setup();

    try {
        $options = mtgdistribute_build_pattern_options();
    } catch (unsafe_regex_exception $e) {
        print_error($e->getMessage(), 'block_mtgdistribute');
    }
    foreach($alis_data as $alis) {
        $form = '';
        if($patterns = get_records('mtgdistribute_patterns', 'alisdataid', $alis->id)) {
            $break = 1;
            
            foreach ($patterns as $pattern) {

                try {
                    $extraoptions = mtgdistribute_build_pattern_options($pattern->pattern);
                } catch (unsafe_regex_exception $e) {
                    print_error($e->getMessage(), 'block_mtgdistribute');
                }

                $form .= '<select name="alispatterns['.$alis->id.']['.$pattern->id.']">'.$extraoptions.'</select>';
                if((count($patterns) > 1 && $break < count($patterns)) || $addfield == $alis->id) {
                    $form .= '<br />';
                }
                $break++;
            }

            if ($addfield == $alis->id) {
                $form .= '<select name="alispatterns['.$alis->id.'][]">'.$options.'</select>';
            }
            
        } else {
            $form .= '<select name="alispatterns['.$alis->id.'][]">'.$options.'</select>';
        }
                
        $form .= '<input type="submit" value="+" name="addpattern['.$alis->id.']" title="Save changes and Add another pattern" />';


        $row = array();
        $row[] = $alis->qualification;
        $row[] = '<a name="alis'.$alis->id.'">'.$alis->subject.'</a>';
        $row[] = $form;
        $row[] = $alis->gradient;
        $row[] = $alis->intercept;
        $table->add_data($row);
    }
}

$navlinks = array();
$navlinks[] = array('name' => get_string('mtgs', 'block_mtgdistribute'),
                    'type' => 'misc');
$navlinks[] = array('name' => get_string('alisdata', 'block_mtgdistribute'),
                    'type' => 'misc');
$nav = build_navigation($navlinks);
print_header_simple(get_string('alisdata', 'block_mtgdistribute'), get_string('alisdata', 'block_mtgdistribute'), $nav);
mtgdistribute_print_tabs(1);

echo '<h2>'.get_string('alisdata', 'block_mtgdistribute').'</h2>';
echo '<p>'.get_string('configalis', 'block_mtgdistribute').'</p>';
if(isset($output)) {
    echo $output;
}
$uploadform->display();
if(isset($table)) {
    echo '<p>'.get_string('explainpatterns', 'block_mtgdistribute', $config).'</p>';
    echo '<form action="'.$CFG->wwwroot.'/blocks/mtgdistribute/alisdata.php" method="post">';
    $table->print_html();
    echo '<input type="submit" name="savepatterns" value="'.get_string('savechanges').'" />
        </form>';
}
print_footer();

?>
